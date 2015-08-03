import rsa;
import os;
import socket;
import urllib.request;

class RSAManager:
    """
    Classe servant a la gestion des clefs et chiffrements RSA.
    """
    def __init__(self):
        self._Private = None;
        self._Public = None;

    def encrypt(self, data_to_crypt, pubKey):
        """
        Cette methode va chiffrer des donnees avec la clefs publique de l'utilisateur qui va devoir
        dechiffrer ces donnees.
        Elle prend en parametre les donnes a chiffer 'data_to_crypt', ainsi que la clef publique de l'utilisateur
        concerne 'pubKey'.
        """
        return rsa.encrypt(data_to_crypt, pubKey);

    def decrypt(self, data_to_decrypt):
        """
        Cette methode va dechiffrer des donnees recues en parametre 'data_to_decrypt' grace a la clef privee
        generee auparavant.
        """
        if self._Private is None:
            raise Exception('No private key generated to crypt. Run initPersonnalKeys please.');
        return rsa.decrypt(data_to_decrypt, self._Private);

    def getAvailablePublicKeys(self, keysFolder):
        """
        Methode qui lit le dossier 'keysFolder' recu en parametre et recupere les hotes pour lesquels
        les clefs publiques sont disponibles. Elle recupere le nom du fichier, retire le '.pub' et
        ajoute dans la liste.
        Cette liste est retournee.
        """
        ret = [];
        for root, _, files in os.walk(keysFolder):
            for f in files:
                if '.pub' in f:
                    ret.append(str(f).split('.pub')[0]);
        return ret;

    def getPublicKeyFromFile(self, filename):
        """
        Cette methode ouvre le fichier 'filename' cense contenir une clef publique, lit le fichier et convertit le
        contenu en clef publique servant a chiffrer des donnees.
        """
        with open(filename, 'r') as f:
            keydata = f.read();
        key = rsa.PublicKey.load_pkcs1(bytes(keydata, 'utf-8'));
        f.close();
        return key;

    def getPrivateKeyFromFile(self, filename):
        """
        Cette methode ouvre et lit le fichier 'filename' cense contenir une clef privee.
        Le contenu de ce fichier est ensuite convertit en clef privee servant a dechiffrer les messages.
        """
        with open(filename, 'r') as f:
            keydata = f.read();
        key = rsa.PrivateKey.load_pkcs1(keydata);
        f.close();
        return key;

    def askForPublicKey(self, fileurl, filetosave):
        """
        Cette methode prend 2 parametres: 'fileurl' qui est une string contenant l'url de l'emplacement d'un fichier de clef publique.
        Elle recupere ce fichier grace a une requete HTTP.
        'filetosave' est une string contenant le nom du fichier a ecrire en local pour stocker le contenu du
        fichier recu.
        """
        try:
            filesaved, header = urllib.request.urlretrieve(fileurl, filetosave);
        except urllib.error.HTTPError as e:
            print('[ ERROR ]: Unable to retrieve public key file from remote host.');
            if e.code == 404:
                print('Reason: ' + e.reason);

    def initPersonnalKeys(self, personnalKeyFolder, publicKeyFolder, keySize):
        """
        Cette fonction va initialiser les 2 clefs privee et publique pour le localhost.
        Si les fichiers contenant les clefs de l'hote n'existent pas elles sont generees
        puis stockees dans le bon dossier et le bon fichier.
        """
        publicFile = publicKeyFolder + '/' + socket.gethostname() + '.pub';
        privateFile = personnalKeyFolder + '/' + socket.gethostname();
        if not os.path.exists(publicFile) or not os.path.exists(privateFile):
            print('Generating keys files...');
            (pub, priv) = rsa.newkeys(keySize);
            keydata = pub.save_pkcs1();
            personnalPublicKeyFile = open(publicFile, 'w');
            personnalPublicKeyFile.write(str(keydata.decode('UTF-8')));
            personnalPublicKeyFile.close();
            print('[ DONE ]: Generated public key.');
            keydata = priv.save_pkcs1();
            personnalPrivKeyFile = open(privateFile, 'w');
            personnalPrivKeyFile.write(str(keydata.decode('UTF-8')));
            personnalPrivKeyFile.close();
            print('[ DONE ]: Generated private key');
        with open(privateFile, 'r') as privFile:
            keydata = privFile.read();
        self._Private = rsa.PrivateKey.load_pkcs1(bytes(keydata, 'utf-8'));
