#!/bin/bash

# Generate all of the reports, in the correct order.

/bin/bash ./genreports.sh
/bin/bash ./gencsv-1.sh  
/bin/bash ./genindex.sh  
/bin/bash ./genindices.sh  
