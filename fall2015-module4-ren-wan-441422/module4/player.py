class Player:
    # constructor:
    def __init__(self, name, atBat, hits):
        self.name = name
        self.atBat = atBat
        self.hits = hits
        self.avgBat = self.hits / float(self.atBat)
    def __lt__(self, other):
        return self.avgBat < other.avgBat
    def getInfo(self):
        return "%-*s:   %.3f" % (20, self.name, self.avgBat)