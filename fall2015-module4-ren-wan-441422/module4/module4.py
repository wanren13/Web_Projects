import sys
from team import Team

if __name__ == "__main__":
    if len(sys.argv) < 2:
        sys.exit("Usage: %s filename(or URL)\n" % sys.argv[0])

    path = sys.argv[1]

    try:
        team = Team(path)
        team.listTeamMembers()
    except NameError:
      sys.exit("Oops ")

