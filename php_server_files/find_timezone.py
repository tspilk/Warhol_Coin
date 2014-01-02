# See https://github.com/tspilk/find-timezone for latest revision of this code #

import argparse

# -- Command Line Options -- #
ARG=argparse.ArgumentParser(\
      description="Get Time zone for given coords")

ARG.add_argument('-t', '--lat', help="Latitude", dest="lat",
default="")
ARG.add_argument('-g', '--lon', help="Longitude", dest="lon",
default="")


# -- Parsing passed arguments to variables -- #
args=ARG.parse_args()

lat = float(args.lat)
lon = float(args.lon)


f = open('timezones.dat','r')
data = f.readlines()

formatted = []
index_list = []
lon_list = []
lon_list_float = []
lat_list =[]

for i in data:
    i = i.strip().split(' ')
    formatted.append( (i[0],(i[1],i[2])) )

my_dict = dict(formatted)

for i in my_dict:
    if float(i) > lat-0.05 and float(i) < lat+0.05:
      lon_list.append(my_dict[i][0])
      lon_list_float.append(float(my_dict[i][0]))
      lat_list.append(i)

result = min(enumerate(lon_list_float), key=lambda x: abs(x[1]-lon))
#print "Closest Match: "+ lat_list[result[0]]+', '+lon_list[result[0]]
print dict(formatted)[lat_list[result[0]]][1]
