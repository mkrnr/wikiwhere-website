import sys
import re
import collections
import os
import json
import psutil
import urllib

from wikiwhere.main.article_extraction import ArticleExtraction
from wikiwhere.utils import json_writer
from wikiwhere.main.count_generation import CountGeneration

#create NamedTuple type for loading the world factbook data set
#load pickled data
Country = collections.namedtuple('Country', 'name, gec, iso2c, iso3c, isonum, stanag, tld')

if __name__ == "__main__":

    max_python_processes = 10

    # get the number of running python processes that are currently running
    process_name = "python2"
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
    article_extraction = ArticleExtraction(geodatabase_path,ianadatabase_path,wfbdatabase_path,model_data_path,languages)
    count_generation = CountGeneration()

    language,title = article_extraction.parse_url(article_url)

    #print language

    language_path = os.path.join("articles",language)
    article_path = os.path.join(language_path,title)

    if not os.path.exists(article_path):
        os.makedirs(article_path)

    article_analysis_path = os.path.join(article_path,"analysis.json")

    # TODO change name
    article_classification_general_count_path = os.path.join(article_path,"counts-classification-general.json")

    if new_crawl or not os.path.isfile(article_analysis_path):
        # exit of too many python programs are already running
        if python_process_count > max_python_processes:
           print "busy"
           sys.exit(1)
        # generate new article
        collected_features = article_extraction.collect_features(article_url)
        collected_features_with_prediction = article_extraction.add_predictions(language,collected_features)
        collected_features_array = article_extraction.get_as_array(collected_features_with_prediction)


        classification_general_counts = count_generation.generate_counts(collected_features_array, "classification-general")
        classification_general_counts_array = count_generation.get_as_array(classification_general_counts)

        # generate directory if it doesn't exist
        if not os.path.exists(language_path):
            os.makedirs(language_path)

        if len(collected_features_array) > 0:
            # write generated file
            json_writer.write_json_file(collected_features_array, article_analysis_path)
            json_writer.write_json_file(classification_general_counts_array, article_classification_general_count_path)
        else:
            print "empty"
            sys.exit(0)

    # load existing article from JSON
    #with open(article_path) as data_file:
    #    data = json.load(data_file)

    print article_path
