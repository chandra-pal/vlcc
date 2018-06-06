#! /bin/bash

#script to copy files from one server to another 

scp ec2-user@52.221.243.74:/home/ec2-user/* /home/ec2-user


#scp username@serverip:/sourceaddress/ destinationaddress
#when you are logged into the destination servr


#script when you are logged in the source server
scp /home/ec2-user/testfile2 ec2-user@52.221.243.74:/home/ec2-user/

#CHANGE FILE PERMISSION AFTER UPLOADING THE FILES
ssh 52.221.243.74 chmod 644 /home/ec2-user/testfile2


#!/bin/sh
for i in `more userlist.txt `
do
echo $i
adduser $i
done


###Create an encrypted password
###You need to create encrypted password using perl crypt():
$ perl -e 'print crypt("password", "salt"),"\n"'