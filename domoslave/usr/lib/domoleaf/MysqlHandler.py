#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import mysql.connector;

## Mysql Master daemon management class.
# Built with 'username', 'password' and 'database' for the connection to the server.
class MysqlHandler:

    ## The constructor.
    #
    # @param username
    # @param passwd
    # @param db
    def __init__(self, username, passwd, db):
        self.connection = mysql.connector.connect(unix_socket = '/var/run/mysqld/mysqld.sock', user = username, password=passwd, host='localhost', database=db);

    ## Gets a description of a table.
    #
    # @param table The table name in which retrieve the fields.
    #
    # @return The field names.
    def get_field_names_from_table(self, table):
        query = 'DESCRIBE '+table;
        cursor = self.connection.cursor(buffered=True);
        res = [];
        append = res.append;
        cursor.execute(query);
        for item in cursor:
            append(item[0]);
        return res;

    ## Function used to send a personnal query, which is not proposed by this class.
    #
    # @param query The query to execute.
    #
    # @return The result of the query.
    def personnal_query(self, query):
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        res = [];
        append = res.append;
        for item in cursor:
            append(item);
        return res;

    ## Inserts datas in a table.
    #
    # @param table The table in which insert data.
    # @param field_names The field names for the value to insert.
    # @param data_values The values to insert.
    def insert_datas_in_table(self, table, field_names, data_values):
        cursor = self.connection.cursor(buffered=False);
        query = "INSERT INTO "+table+" (";
        i = 0;
        field_len = len(field_names)
        while i < field_len - 1:
            query += field_names[i]+', ';
            i += 1;
        query += field_names[field_len - 1]+") VALUES (";
        i = 0;
        while i < field_len - 1:
            query += "%s, ";
            i += 1;
        query += "%s)";
        cursor.execute(query, data_values);

    ## Updates data in table. If the data having to be updated do not exist, they are created and inserted.
    # Else, they are updated.
    #
    # @param table The table in which update / insert the data.
    # @param data_values_ref The reference data to update. If not found, the data are inserted.
    # @param data_to_update The new value of the data.
    def update_datas_in_table(self, table, data_values_ref, data_to_update):
        cursor = self.connection.cursor(buffered=True);
        try:
            query_insert = "INSERT INTO "+table+" (";
            for data_name in data_values_ref.keys():
                query_insert += data_name+", ";
            for data_name in data_to_update.keys():
                query_insert += data_name+", ";
            query_insert = query_insert[:len(query_insert) - 2];
            query_insert += ") VALUES (";
            i = 0;
            while i < len(data_values_ref) + len(data_to_update):
                query_insert += "%s, ";
                i += 1;
            query_insert = query_insert[:len(query_insert) - 2];
            query_insert += ")";
            data = tuple(data_values_ref.values()) + tuple(data_to_update.values());
            cursor.execute(query_insert, data);
        except Exception as e:
            query_update = "UPDATE "+table+" SET ";
            for data_name in data_to_update.keys():
                data = data_to_update[data_name];
                query_update += data_name+"=\""+data+"\", ";
            query_update = query_update[:len(query_update) - 2];
            query_update += " WHERE ";
            for data_name in data_values_ref.keys():
                data = data_values_ref[data_name];
                query_update += data_name+"=\""+data+"\" AND ";
            query_update = query_update[:len(query_update) - 5];
            cursor.execute(query_update);

    ## Erases the content of a table.
    #
    # @param table The table to empty.
    def reset_table(self, table):
        query = "DELETE FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);

    ## Gets some fields values from a table.
    #
    # @param table The table to query.
    # @param names The names of the field to retrieve.
    def get_datas_from_table_with_names(self, table, names):
        res = [];
        append = res.append;
        query = "SELECT ";
        for name in names:
            query += name+", ";
        query = query[:len(query) - 2];
        query += " FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        for item in cursor:
            append(item);
        return res;

    ## Retrieves all the data from a table.
    #
    # @param table The table to query.
    #
    # @return Array containing the result.
    def get_all_datas_from_table(self, table: str):
        res = [];
        append = res.append;
        query = "SELECT * FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        for item in cursor:
            append(item);
        return res;

    ## Commits the changes done to database.
    def updatedb(self):
        self.connection.commit();

    ## Closes the connection to mysql server.
    def close(self):
        self.connection.close();
