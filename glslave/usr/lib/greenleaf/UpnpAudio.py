import re;
from Upnp import *;

UPNP_IP_ADDR = '192.168.1.69';
UPNP_PORT = 1400;

class UpnpAudio(Upnp):
    """
    Class inherited by 'Upnp' to communicate with audio devices running Upnp.
    """
    def __init__(self, ip_addr = UPNP_IP_ADDR, port = UPNP_PORT):
        Upnp.__init__(self, ip_addr, port);

    def set_play(self, speed = 1, instance_id = 0):
        """
        Start a song. 'speed' is the playing speed.
        """
        Upnp.send_request(self, 'Play', {'InstanceID': instance_id, 'Speed': speed});

    def set_pause(self, instance_id = 0):
        """
        Pause the current playing song.
        """
        Upnp.send_request(self, 'Pause', {'InstanceID': instance_id});

    def set_stop(self, instance_id = 0):
        """
        Stop current playing song.
        """
        Upnp.send_request(self, 'Stop', {'InstanceID': instance_id});

    def get_mute(self, channel = 'Master', instance_id = 0):
        """
        Returns a boolean saying whether the device is muted or not.
        """
        mute = Upnp.send_request(self, 'GetMute', {'InstanceID': instance_id, 'Channel': channel});
        match = re.search("<CurrentMute>(.*)</CurrentMute>", mute).group(1);
        return match;

    def set_mute(self, mute = 0, channel = 'Master', instance_id = 0):
        """
        Mute or unmune a device.
        if 'mute' is 0 the device gets unmuted, and if 'mute' is 1, the device is muted.
        """
        mute = self.get_mute(channel, instance_id);
        if mute != '1':
            mute = 1;
        else:
            mute = 0;
        Upnp.send_request(self, 'SetMute', {'InstanceID': instance_id, 'Channel': channel, 'DesiredMute': mute});

    def get_volume(self, channel = 'Master', instance_id = 0):
        """
        Retrieves the current volume value.
        """
        volume = Upnp.send_request(self, 'GetVolume', {'InstanceID': instance_id, 'Channel': channel});
        match = re.search('<CurrentVolume>(.*)</CurrentVolume>', volume).group(1);
        return int(match);

    def set_volume(self, desired_volume = 0, channel = 'Master', instance_id = 0):
        """
        Sets a volume value.
        """
        mute = self.get_mute(channel, instance_id);
        if mute == '1':
            Upnp.send_request(self, 'SetMute', {'InstanceID': instance_id, 'Channel': channel, 'DesiredMute': 0});
        return Upnp.send_request(self, 'SetVolume', {'InstanceID': instance_id,
                                               'Channel': channel,
                                               'DesiredVolume': desired_volume});

    def set_volume_inc(self, channel = 'Master', instance_id = 0):
        """
        Increase the volume.
        """
        volume = self.get_volume(channel, instance_id);
        self.set_volume(volume + 2, channel, instance_id);

    def set_volume_dec(self, channel = 'Master', instance_id = 0):
        """
        Decrease the volume.
        """
        volume = self.get_volume(channel, instance_id);
        if volume >= 2:
            self.set_volume(volume - 2, channel, instance_id);
        else:
            self.set_volume(0, channel, instance_id);

    def set_next(self, instance_id = 0):
        """
        Send request to read next song.
        """
        Upnp.send_request(self, 'Next', {'InstanceID': instance_id});

    def set_previous(self, instance_id = 0):
        """
        Send request to read previous song.
        """
        Upnp.send_request(self, 'Previous', {'InstanceID': instance_id});

    def set_record(self, instance_id = 0):
        """
        Set a device in 'record' mode.
        """
        Upnp.send_request(self, 'Record', {'InstanceID': instance_id});
