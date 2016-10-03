## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

from Logger import *;
import configparser;
import sys;
sys.path.append("/usr/lib/domoleaf");

LOG_FILE        = '/var/log/domoleaf/domoslave.log';
LOG_FLAG        = True;

## Configuration files parsing class.
class DaemonConfigParser:

    ## The constructor.
    #
    # @param filename The filename of the file to parse.
    def __init__(self, filename):
        ## Instance of the logger object for fomatting and printing logs
        self.logger = Logger(LOG_FLAG, LOG_FILE);
        self._Filename = filename;
        self._Config = configparser.ConfigParser();
        if not self._Config.read(filename):
            raise Exception("DaemonConfigParserError: " + filename + ": No such file or directory");

    ## Return the value of a field inside a section in the open configuration file.
    #
    # @param sectionName The name of the section of the section in which check the value.
    # @param valueName The name of the key from which the value is wanted.
    #
    # @return The value wanted if found, else None.
    def getValueFromSection(self, sectionName, valueName):
        if sectionName in self._Config:
            if valueName in self._Config[sectionName]:
                return self._Config[sectionName][valueName];
            else:
                print("[ WARNING ]: '", valueName, "' not defined in section '", sectionName, "'");
                return None;
        else:
            print("[ WARNING ]: '", sectionName, "' not defined in ", self._Filename);
            return None;

    ## Returns the content of a section inside the open configuration file.
    #
    # @param sectionName The name of the section to get.
    #
    # @return The content of the section if found, else None.
    def getSection(self, sectionName):
        if sectionName in self._Config:
            return self._Config[sectionName];
        else:
            print("[ ERROR ]: '", sectionName, "' not defined in ", self._Filename);
            return None;

    ## Write the content of a section inside the open configuration file.
    #
    # @param sectionName The name of the section to which write the new key - value pair.
    # @param valueName The name of the key to write with its value.
    # @param value The value to write.
    # @return None
    def writeValueFromSection(self, sectionName, valueName, value):
        try:
            if not self.getValueFromSection(sectionName, valueName):
                return;
            self._Config[sectionName][valueName] = value;
            with open(self._Filename, 'w') as filename:
                self._Config.write(filename);
        except Exception as e:
            self.logger.error(e);
