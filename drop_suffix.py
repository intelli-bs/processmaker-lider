import glob
import os
from os import listdir
dir = os.path.dirname(__file__)

for f in listdir(dir):
    print(str(f))


