import urllib2
import sys, os
from player import Player
from collections import defaultdict
import re

class Team:
    # constructor:
    def __init__(self, path):
        self.path = path
        self.players = []
        self.parseData()

    def parseData(self):
        file_name_regex = re.compile(r".*?(?P<file_name>[\w]+\-\d{4}.txt)$")
        file_name_match = file_name_regex.match(self.path)
        self.name = file_name_match.group('file_name')
        try:
            data = urllib2.urlopen(self.path)
        except ValueError:  # invalid URL
            if not os.path.exists(self.path):
                sys.exit("Error: File '%s' not found" % self.path)
            data = open(self.path)

        regex = re.compile(r"(?P<name>^[\w]+\s[\w]+)\s\bbatted\b\s(?P<bats>\d+)\s\btimes with\b\s(?P<hits>\d+).*")

        atBatted = defaultdict(lambda: 0)
        hits = defaultdict(lambda: 0)
        player_names = set()

        for line in data:
            match = regex.match(line)
            if match:
                player_name = match.group('name')
                player_names.add(player_name)
                atBatted[player_name] += int(match.group('bats'))
                hits[player_name] += int(match.group('hits'))

        data.close()

        for player_name in player_names:
            numOfBatted = atBatted[player_name]
            numOfHits = hits[player_name]
            self.players.append(Player(player_name, numOfBatted, numOfHits))

        self.players.sort(reverse=True)


    def listTeamMembers(self):
        print self.name + "\n"
        for player in self.players:
            print player.getInfo()