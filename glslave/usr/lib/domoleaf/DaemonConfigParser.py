from Logger import *;
import configparser;
import sys;
sys.path.append("/usr/lib/domoleaf");

LOG_FILE        = '/var/log/glslave.log';
LOG_FLAG        = True;

class DaemonConfigParser:
    """
    Configuration files parsing class.
    """
    def __init__(self, filename):
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self._Filename = filename;
        self._Config = configparser.ConfigParser();
        if not self._Config.read(filename):
            raise Exception("DaemonConfigParserError: " + filename + ": No such file or directory");

    def getValueFromSection(self, sectionName, valueName):
        """
        Return the value of a field inside a section in the open configuration file.
        """
        if sectionName in self._Config:
            if valueName in self._Config[sectionName]:
                return self._Config[sectionName][valueName];
            else:
                print("[ WARNING ]: '" + valueName + "' not defined in section '" + sectionName + "'");
                return None;
        else:
            print("[ WARNING ]: '" + sectionName + "' not defined in " + self._Filename);
            return None;

    def getSection(self, sectionName):
        """
        Returns the content of a section inside the open configuration file.
        """
        if sectionName in self._Config:
            return self._Config[sectionName];
        else:
            print("[ ERROR ]: '" + sectionName + "' not defined in " + self._Filename);
            return None;

    def writeValueFromSection(self, sectionName, valueName, value):
        """
        Write the content of a section inside the open configuration file.
        """
        try:
            if not self.getValueFromSection(sectionName, valueName):
                return;
            self._Config[sectionName][valueName] = value;
            with open(self._Filename, 'w') as filename:
                self._Config.write(filename);
        except Exception as e:
            self.logger.error(e);
            
