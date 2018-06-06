#! /bin/bash

#Author Chandra Pal
#Date 18 May 2018



echo " Enter values of A"
read A
echo " Enter value of B"
read B


if [$A -gt $B]
then
echo " $A is greater than $B"
else
echo " $A is smaller than $B"
fi

if [$A -ne $B]

then

 echo "$A is not equal to $B"

else

 echo "$A is equal to $B"

fi
