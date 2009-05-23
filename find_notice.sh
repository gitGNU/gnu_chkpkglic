#!/bin/dash
# This script will check each file in the project directory, except those that have the filenames described in the 
# exclude_files file.
#
# Copyright (C) 2009 Nicodemo Alvaro

# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 3
# of the License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# The alternative of this script is to run this from the command line.

# grep -r -l -f ${sdir}${license}_notice --exclude-from=${sdir}exclude_files ${dir} > ${dir}.$license.type

# The most convienent way to do deal with variables is to set them in the commandline

# To set the variables from the commandline use the export command
# export sdir=${PWD}/chkpkglic
# export license=bsd
# export dir=${PWD}/project_directory

# Description of Variables
# sdir is the script directory. I use this directory because it also contains the license notice files.
# license is the license that you want to see if there is a notice for.
# dir is the directory that contains the project that you need to scan
# PWD is the printed working directory from where you run the command from.


sdir=chkpkglic

# I am not sure how do I set the arguments from the commandline, so I'll have the user do it interactively.

echo "What is the name of the project directory ? Type it and hit the return key"
read dir

# TODO:: The user may type a forward slash at the end of the directory. 

echo "What is the name of license you like to find ? Type it and hit the return key"
read license

grep \
	--recursive  --files-with-matches \
	--file ${sdir}/${license}_notice \
	--exclude-from=${sdir}/exclude_files \
	${PWD}/${dir} \
	> ${PWD}/${dir}.$license.type

echo "This is the file created"

echo ${dir}.${license}.type
