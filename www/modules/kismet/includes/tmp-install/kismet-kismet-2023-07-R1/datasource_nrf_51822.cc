/*
    This file is part of Kismet

    Kismet is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Kismet is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Kismet; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

#include "datasource_nrf_51822.h"

#include "endian_magic.h"

void kis_datasource_nrf51822::handle_rx_datalayer(
    std::shared_ptr<kis_packet> packet,
    const KismetDatasource::SubPacket &report) {
    typedef struct {
        uint8_t monitor_channel;
        int8_t signal;
        int8_t noise;
        uint8_t access_offenses;
        uint8_t reference_access_address[4];
        uint16_t flags_le;
        uint8_t payload[0];
    } __attribute__((packed)) btle_rf;

    // Subset of flags we set
    const uint16_t btle_rf_flag_dewhitened = (1 << 0);
    const uint16_t btle_rf_flag_signalvalid = (1 << 1);
    const uint16_t btle_rf_flag_reference_access_valid = (1 << 5);
    const uint16_t btle_rf_crc_checked = (1 << 10);
    const uint16_t btle_rf_crc_valid = (1 << 11);
    const uint16_t btle_rf_mic_checked = (1 << 12);
    const uint16_t btle_rf_mic_valid = (1 << 13);

    auto &rxdata = report.data();

    // If we can't validate the basics of the packet at the phy capture level,
    // throw it out. We don't get rid of invalid btle contents, but we do get
    // rid of invalid USB frames that we can't decipher - we can't even log them
    // sanely!

    // cc_chunk->length
    // cc_chunk->data

    // so I can get multiple packets back on the data stream from the nrf51822.
    // Not sure if that is by design but we can handle it here
    unsigned char pkt[255];
    memset(pkt, 0x00, 255);
    int pkt_ctr = 0;

    /* the packets they have a padded byte at offset 16.
     * if there is a better way to remove the header < 10
     * and remove the padded byte, please update
     * */
    for (unsigned int xp = 0; xp < rxdata.length(); xp++) {
        if (xp >= 10 && xp != 16) {
            pkt[pkt_ctr] = rxdata[xp];
            pkt_ctr++;
        }
    }

    int8_t channel = rxdata[2];
    int8_t bt_channel = rxdata[2];
    int8_t rssi = rxdata[3] * -1;            //?
    int8_t valid_pkt = rxdata[1] & (1 << 0); // first byte

    if (valid_pkt) {
        // make the new header and fill it
        switch (channel) {
            case 37:
                bt_channel = 0;
                break;
            case 38:
                bt_channel = 12;
                break;
            case 39:
                bt_channel = 39;
                break;
            default:
                bt_channel = channel - 2;
        };

        // We can make a valid payload from this much
        auto conv_buf_len = sizeof(btle_rf) + pkt_ctr; // cc_payload_len;
        char conv_buf[conv_buf_len];

        btle_rf *conv_header = reinterpret_cast<btle_rf *>(conv_buf);

        // Copy the actual packet payload into the header
        memcpy(conv_header->payload, &pkt[0], pkt_ctr);

        // Set the converted channel
        conv_header->monitor_channel = bt_channel;

        // RSSI
        conv_header->signal = rssi;

        uint16_t bits = btle_rf_crc_checked;
        if (valid_pkt) {
            bits += btle_rf_crc_valid;
            packet->crc_ok = true;
        }

        // MIC
        bits += btle_rf_mic_checked;

        if (rxdata[1] & (1 << 3))
            bits += btle_rf_mic_valid;

        // should change since we know we are valid
        if (pkt_ctr >= 4) {
            memcpy(
                conv_header->reference_access_address, conv_header->payload, 4);
            bits += btle_rf_flag_reference_access_valid;
        }

        conv_header->flags_le = kis_htole16(bits + btle_rf_flag_signalvalid + btle_rf_flag_dewhitened);


        // Put the modified data into the packet & fill in the rest of the base data info
        auto datachunk = packetchain->new_packet_component<kis_datachunk>();

        if (clobber_timestamp && get_source_remote()) {
            gettimeofday(&(packet->ts), NULL);
        } else {
            packet->ts.tv_sec = report.time_sec();
            packet->ts.tv_usec = report.time_usec();
        }

        datachunk->dlt = KDLT_BTLE_RADIO;

        packet->set_data(conv_buf, conv_buf_len);
        datachunk->set_data(packet->data);

        get_source_packet_size_rrd()->add_sample(conv_buf_len, time(0));

        packet->insert(pack_comp_linkframe, datachunk);

        // Generate a l1 radio header and a decap header since we have it
        // computed already
        auto radioheader = packetchain->new_packet_component<kis_layer1_packinfo>();
        radioheader->signal_type = kis_l1_signal_type_dbm;
        radioheader->signal_dbm = conv_header->signal;
        radioheader->freq_khz = (2400 + (channel)) * 1000;
        radioheader->channel = fmt::format("{}", (channel));
        packet->insert(pack_comp_radiodata, radioheader);

        auto decapchunk = packetchain->new_packet_component<kis_datachunk>();
        decapchunk->dlt = KDLT_BLUETOOTH_LE_LL;
        decapchunk->set_data(packet->data.substr(sizeof(btle_rf), pkt_ctr));
        packet->insert(pack_comp_decap, decapchunk);
    }
}

