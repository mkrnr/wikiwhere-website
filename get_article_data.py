import sys
import re
import collections
import os
import json
import psutil
import urllib
import datetime

from wikiwhere.main.article_extraction import ArticleExtraction
from wikiwhere.utils import json_writer
from wikiwhere.plot_data_generation.count_generation import CountGeneration
from wikiwhere.plot_data_generation.map_data_generation import MapDataGeneration

#create NamedTuple type for loading the world factbook data set
#load pickled data
Country = collections.namedtuple('Country', 'name, gec, iso2c, iso3c, isonum, stanag, tld')

if __name__ == "__main__":

    max_python_processes = 10

    # get the number of running python processes that are currently running
    process_name = "python2.7"
    python_process_count = 0
    for proc in psutil.process_iter():
        process = psutil.Process(proc.pid)# Get the process info using PID
        pname = process.name()# Here is the process name
        #print pname
        if pname == process_name:
            python_process_count += 1

    # get article url
    article_url_encoded =  sys.argv[1]
    article_url =  urllib.unquote(article_url_encoded)

    if "wikipedia.org" not in article_url:
        print "not wiki"
        sys.exit(0)

    new_crawl = False
    if len(sys.argv)>2:
        if  sys.argv[2] == "true":
            new_crawl = True

    base_dir=os.path.dirname(os.path.realpath(__file__))
    sys.path.append(base_dir)

    data_path = os.path.join(base_dir,"data")
    database_path = os.path.join(data_path,"databases")
    geodatabase_path =os.path.join(database_path,"GeoLite2-Country.mmdb")
    ianadatabase_path =os.path.join(database_path,"iana.p")
    wfbdatabase_path =os.path.join(database_path,"wfb.p")

    model_data_path = os.path.join(data_path,"models")

    languages = ["de", "en","es","fr","general","it","nl","sv","uk"]
    features = ["ip-location","tld-location","website-language"]
    article_extraction = ArticleExtraction(geodatabase_path,ianadatabase_path,wfbdatabase_path,model_data_path,languages)

    count_generation = CountGeneration()
    map_data_generation = MapDataGeneration()

    language,title = article_extraction.parse_url(article_url)

    #print language

    language_path = os.path.join("articles",language)
    article_path = os.path.join(language_path,title)

    article_info_path = os.path.join(article_path,"info.json")
    article_plots_redirect_path = os.path.join(article_path,"visualization-redirect.php")

    article_analysis_path = os.path.join(article_path,"analysis.json")

    if new_crawl or not os.path.isfile(article_analysis_path):
        # exit of too many python programs are already running
        if python_process_count > max_python_processes:
           print "busy"
           sys.exit(1)
        # generate new article
        collected_features = article_extraction.collect_features(article_url)
        collected_features_with_prediction = article_extraction.add_predictions(language,collected_features)
        collected_features_with_fixed_outliers = article_extraction.fix_outliers(collected_features_with_prediction,"classification","classification-fixed",features)
        collected_features_with_fixed_outliers = article_extraction.fix_outliers(collected_features_with_fixed_outliers,"classification-general","classification-general-fixed",features)
        collected_features_array = article_extraction.get_as_array(collected_features_with_fixed_outliers)

        if len(collected_features_array) > 0:

            # generate directories if they don't exist
            if not os.path.exists(article_path):
                os.makedirs(article_path)
            if not os.path.exists(language_path):
                os.makedirs(language_path)

            json_writer.write_json_file(collected_features_array, article_analysis_path)

            count_features = ["ip-location","tld-location","website-language","classification-fixed","classification-general-fixed"]
            for count_feature in count_features:
                classification_general_counts = count_generation.generate_counts(collected_features_array, count_feature)
                classification_general_counts_array = count_generation.get_as_array(classification_general_counts, 20)

                article_count_path = os.path.join(article_path,"counts-"+count_feature+"-top-20.json")
                json_writer.write_json_file(classification_general_counts_array, article_count_path)

            # generate map data
            map_data = map_data_generation.generate_map_data_array(collected_features_array,"classification-general-fixed")
            article_map_data_path = os.path.join(article_path,"map-data.json")
            json_writer.write_json_file(map_data, article_map_data_path)

            # get execution date
            now = datetime.datetime.now()
            time_info = {}
            time_info["analysis-date"]= now.strftime("%Y-%m-%d")
            time_info["analysis-time"]= now.strftime("%H:%M:%S")
            json_writer.write_json_file(time_info, article_info_path)

            # write php redirect file
            redirect_string = "<?php header(\"Location: http://wikiwhere.west.uni-koblenz.de/article.php?url="+article_url+"\"); ?>"
            text_file = open(article_plots_redirect_path, "w")
            text_file.write(redirect_string)
            text_file.close()

        else:
            print "empty"
            sys.exit(0)

    print article_path
