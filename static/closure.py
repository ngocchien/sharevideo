#!/usr/bin/python2.7

import os, sys, time

def compile(source, destination):
    command = "java -jar " + static_path + "/compiler.jar --js " + source + " --js_output_file " + destination
    os.system(command)

static_path = os.path.realpath(os.path.dirname(sys.argv[0]))

def closured( path ):
	if os.path.isdir(path):
		list = os.listdir(path)
		for f in list:
			if (f == ".svn" or f == '.' or f == '..'):
				continue
			source = path + '/' + f
			if(os.path.isdir(source)):
				closured(source)
			if( f.find(".debug.") != -1):
				ftime = os.stat(source).st_mtime
				mtime = time.time()
				destination = source.replace(".debug.", ".")
				if(ftime > mtime - 43200):
					compile(source, destination)
					print "Closured: " + destination
f_path = static_path + "/fe/js"
closured( f_path )




