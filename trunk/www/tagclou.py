#!/usr/bin/python
'''
    Relational Tag Cloud sketch
    Copyright (c) 2007, Gabor Papp (gabor.papp at mndl hu)
    All rights reserved.

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
'''

import urllib
import re
import sys

url_deli_tags = 'http://del.icio.us/tag/?sort=freq'
url_deli_host = 'http://del.icio.us/'

number_of_tags = 500 # the number of most frequent tags examined
items_per_tag = 100  # the number of items examined for each tag

fontsize_min = 10    # fontsize based on the frequency of tags
fontsize_max = 25

tag_relfreq_thres = 0.08 # relation frequency threshold

nodes = []
nodesu = []
nodesw = []
edges = {}

item_tags = {}

# returns a list of tuples (tag_url, tag)
def deli_read_tag_links():
    tags_page = urllib.urlopen(url_deli_tags).read()
    pattern = '<a href="(/tag/.+?)" class=.+?>(.+?)<\/a>'
    return re.findall(pattern, tags_page)

# downloads items associated with the current tag and finds all item tags
# filter out unpopular tags
def deli_get_item_tags(tag_url, main_tags):
    items_page = urllib.urlopen(url_deli_host + tag_url + '?setcount=%d' % \
        items_per_tag).read()
    post_pattern = '<li class="post" key="(.+?)">(.+?)<\/li>'
    
    global item_tags
    items = re.findall(post_pattern, items_page, re.DOTALL)
    for (key, data) in items:
        if key not in item_tags:
            tag_pattern = '<a class="tag" href=".+?">(.+?)</a>'
            tags = re.findall(tag_pattern, data)
            tags = filter(lambda x, main_tags=main_tags: x in main_tags, tags)
            item_tags[key] = tags

def uniq(alist):
    set = {}
    map(set.__setitem__, alist, [])
    return set.keys()

# makes graph structure from tags
def make_graph():
    global nodes, nodesw, nodesu, edges

    for tags in item_tags.values():
        item_nodes = []
        for tag in tags:
            item_nodes.append(tag)
        
        for a in item_nodes:
            for b in item_nodes:
                if a != b:
                    if (a < b):
                        edge = (a, b)
                    else:
                        edge = (b, a)
                    if edge not in edges:
                        edges[edge] = 1
                    else:
                        edges[edge] += 1
        nodes += item_nodes

    global min_count, max_count
    nodesu = uniq(nodes)
    nodesw = []
    min_count = 999999
    max_count = -999999
    for n in nodesu:
        nc = nodes.count(n)
        if nc < min_count:
            min_count = nc
        elif nc > max_count:
            max_count = nc
        nodesw.append((n, nc))
    #nodes = nodesu

# prints MXML file to output
def print_springgraph():
    print '<?xml version="1.0" encoding="utf-8"?>'
    print '<mx:Application xmlns:mx="http://www.adobe.com/2006/mxml" ' \
        'layout="absolute" xmlns:adobe="http://www.adobe.com/2006/fc">\n'

    print '<adobe:SpringGraph id="springgraph" width="100%" bottom="0" top="0"'
    print '\tbackgroundColor="#666666" repulsionFactor=".2" xmlNames="[node,edge,source,dest]">'
    print '\t<adobe:dataProvider>\n\t<mx:XML xmlns="">'
    print '\t\t<stuff>'
    
    for i in range(len(nodesw)):
        w = fontsize_min + \
            (nodesw[i][1]-min_count)*(fontsize_max-fontsize_min)/float(max_count-min_count)
        print '\t\t\t<node id="%d" prop="%s" weight="%4.2f"/>' % (i, nodesw[i][0], w)
    for ((a, b), n) in edges.items():
        weight = n/float(nodes.count(a)+nodes.count(b))
        sys.stderr.write('%s(%d)-%s(%d) %d %f\n' % (a, nodes.count(a), b, 
            nodes.count(b), n, weight))
        if weight > tag_relfreq_thres:
            print '\t\t\t<edge source="%d" dest="%d" />' % (nodesu.index(a), nodesu.index(b))

    print '\t\t</stuff>'
    print '\t</mx:XML>'
    print '\t</adobe:dataProvider>'

    print '\t<adobe:itemRenderer>'
    print '\t\t<mx:Component>'
    print '\t\t\t<mx:Label fontSize="{data.data.@weight}" text="{data.data.@prop}" color="#ffffff"/>'
    print '\t\t</mx:Component>'
    print '\t</adobe:itemRenderer>'
    print '</adobe:SpringGraph>'
    print '</mx:Application>'

def main():
    tags = deli_read_tag_links()
    end = min(len(tags), number_of_tags)
    tags = tags[:end]
    (tag_urls, main_tags) = zip(*tags)
    for url in tag_urls:
        sys.stderr.write(url+'\n')
        deli_get_item_tags(url, main_tags)
    make_graph()
    print_springgraph()
    

main()
