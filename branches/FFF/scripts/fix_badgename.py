#!/usr/bin/python

import string
import sys
import commands
import MySQLdb

Subj = 'Program information for FFF 34.'
DbUser = 'FFF'
DbName = 'FFF34Z'
DbHost = 'localhost'
queryString = 'select badgeid,pubsname FROM Participants' # 

# Connect to Database
db = MySQLdb.connect(host=DbHost,user=DbUser,db=DbName)

# Create a Cursor.  The first builds a tuple, the second allows me to
# address each element by name.
#cursor = db.cursor()
cursor = db.cursor (MySQLdb.cursors.DictCursor)

# Execute SQL statement (no ";" needed)
#cursor.execute("SELECT * from CongoDump")

cursor.execute(queryString)

# Get number of rows (to make sure it isn't zero)
numrows = int(cursor.rowcount)
if numrows == 0:
    print 'This query retrieved no results matchng the criteria: %s' % (queryString,)
    sys.exit(2)

# iterate through resultset
for x in range(0,numrows):
    row = cursor.fetchone()
# switch on existing email address
    if row["pubsname"] == '':
        print "Need pubsname for uid: " + row["badgeid"]
    else:
        inqueryString='UPDATE CongoDump SET badgename="' + row["pubsname"] + '" WHERE badgeid=' + row["badgeid"] + ';'
        print inqueryString
