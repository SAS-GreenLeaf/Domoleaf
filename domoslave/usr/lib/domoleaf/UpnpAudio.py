## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import re;
from Upnp import *;

UPNP_IP_ADDR = '192.168.1.69';
UPNP_PORT = 1400;

## Class inherited by 'Upnp' to communicate with audio devices running Upnp.
class UpnpAudio(Upnp):

    ## The constructor.
    #
    # @param ip_addr Address of a device on local network running Upnp (default UPNP_IP_ADDR).
    # @param port Port on which the Upnp device is listenning (default UPNP_PORT).
    def __init__(self, ip_addr = UPNP_IP_ADDR, port = UPNP_PORT):
        Upnp.__init__(self, ip_addr, port);

    ## Checks option_id and does an action.
    #
    # @param json_obj JSON Object containing option_id.
    # @return None
    def action(self, json_obj):
        option_id = int(json_obj['data']['option_id'])
        if option_id == 363:
            self.set_play()
        elif option_id == 364:
            self.set_pause();
        elif option_id == 365:
            self.set_stop();
        elif option_id == 366:
            self.set_next();
        elif option_id == 367:
            self.set_previous();
        elif option_id == 368:
            mute = self.get_mute();
            mute = (int(mute)+1)%2;
            self.set_mute(mute = mute);
        elif option_id == 383:
            self.set_volume(desired_volume = int(json_obj['data']['value']));

    ## Starts a song.
    #
    # @param speed The speed of the song being played (default 1).
    # @param instance_id The instance ID of the device.
    # @return None
    def set_play(self, speed = 1, instance_id = 0):
        Upnp.send_request(self, 'Play', {'InstanceID': instance_id, 'Speed': speed});

    ## Pauses the current playing song.
    #
    # @param instance_id The instance ID of the device.
    # @return None
    def set_pause(self, instance_id = 0):
        Upnp.send_request(self, 'Pause', {'InstanceID': instance_id});

    ## Stops the current playing song.
    #
    # @param instance_id The instance ID of the current playing song.
    # @return None
    def set_stop(self, instance_id = 0):
        Upnp.send_request(self, 'Stop', {'InstanceID': instance_id});

    ## Checks whether the device is muted or not.
    #
    # @param channel The ID of the channel.
    # @param instance_id The instance ID of the device.
    #
    # @return True if the device is muted, False otherwise.
    def get_mute(self, channel = 'Master', instance_id = 0):
        mute = Upnp.send_request(self, 'GetMute', {'InstanceID': instance_id, 'Channel': channel});
        match = re.search("<CurrentMute>(.*)</CurrentMute>", mute).group(1);
        return match;

    ## Mutes or unmutes a device.
    #
    # @param mute If set to 0, the device gets unmuted, and if it is 1, the device gets muted.
    # @param channel The name of the channel.
    # @param instance_id The instance ID of the device.
    # @return None
    def set_mute(self, mute = 0, channel = 'Master', instance_id = 0):
        if mute != 1:
            mute = 0;
        Upnp.send_request(self, 'SetMute', {'InstanceID': instance_id, 'Channel': channel, 'DesiredMute': mute});

    ## Gets the current volume value.
    #
    # @param channel The name of the channel.
    # @param instance_id The instance ID of the device.
    #
    # @return The current volume of the device.
    def get_volume(self, channel = 'Master', instance_id = 0):
        volume = Upnp.send_request(self, 'GetVolume', {'InstanceID': instance_id, 'Channel': channel});
        match = re.search('<CurrentVolume>(.*)</CurrentVolume>', volume).group(1);
        return int(match);

    ## Sets the value of the volume.
    #
    # @param desired_volume The value desired for the volume (default 0).
    # @param channel The name of the channel.
    # @param instance_id The instance ID of the device.
    # @return The result of the Upnp request sent.
    def set_volume(self, desired_volume = 0, channel = 'Master', instance_id = 0):
        mute = self.get_mute(channel, instance_id);
        if mute == '1':
            self.set_mute(0, channel, instance_id);
        return Upnp.send_request(self, 'SetVolume', {'InstanceID': instance_id,
                                               'Channel': channel,
                                               'DesiredVolume': desired_volume});

    ## Increases the volume of the device.
    #
    # @param channel The name of the channel.
    # @param instance_id The instance ID of the device.
    # @return None
    def set_volume_inc(self, channel = 'Master', instance_id = 0):
        volume = self.get_volume(channel, instance_id);
        self.set_volume(volume + 2, channel, instance_id);

    ## Decreases the volume of the device.
    #
    # @param channel The name of the channel.
    # @param instance_id The instance ID of the device.
    # @return None
    def set_volume_dec(self, channel = 'Master', instance_id = 0):
        volume = self.get_volume(channel, instance_id);
        if volume >= 2:
            self.set_volume(volume - 2, channel, instance_id);
        else:
            self.set_volume(0, channel, instance_id);

    ## Asks the device to read next song.
    #
    # @param instance_id The instance ID of the device.
    # @return None
    def set_next(self, instance_id = 0):
        Upnp.send_request(self, 'Next', {'InstanceID': instance_id});

    ## Asks the device to read previous song.
    #
    # @param instance_id The instance ID of the device.
    # @return None
    def set_previous(self, instance_id = 0):
        Upnp.send_request(self, 'Previous', {'InstanceID': instance_id});

    ## Sets a device in record mode.
    #
    # @param instance_id The instance ID of the device.
    # @return None
    def set_record(self, instance_id = 0):
        Upnp.send_request(self, 'Record', {'InstanceID': instance_id});
