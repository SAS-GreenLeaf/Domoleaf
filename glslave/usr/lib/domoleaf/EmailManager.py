#!/usr/bin/python3

import smtplib;
from email.mime.text import MIMEText;

SMTP_ADDR = 'smtp.greenleaf.fr';

class EmailManager:
    """
    Class used to send mails.
    """
    def __init__(self,
                 _from = 'localhost',
                 _to = ''):
        self._from = _from;
        self._to = _to;
        self._smtp_server = smtplib.SMTP(SMTP_ADDR);
        self._msg = None;

    def send(self, msg_txt, subject):
        """
        Sends an email.
        'msg_txt' is the core of the mail.
        'subjet' is the subject.
        """
        self._msg = MIMEText(msg_txt);
        self._msg['Subject'] = subject;
        self._msg['From'] = self._from;
        self._msg['To'] = self._to;

    def quit(self):
        """
        Shutting down connection to SMTP server.
        """
        self._smtp_server.quit();
