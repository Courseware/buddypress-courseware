Run this inside the root folder of Courseware plugin.
find ./ -iname "*.php" | xargs xgettext -j -k__ -k_e -k_ -o languages/bpsp.pot -L PHP --no-wrap
