#!/usr/bin/perl -w

use strict;


my $insert_string="";
my $name="";
my $title="";
my $desc="";
my $addinfo="";
my $query="";
my $query_p=0;

opendir(WORKDIR, "../webpages") || warn "Can't open .: $!\n";
my @files = grep(/report.php/, readdir WORKDIR);
closedir(WORKDIR);

foreach my $filename (@files) {
    $insert_string.="INSERT INTO Reports (reportname, reporttitle, reportdescription, reportadditionalinfo, reportquery) VALUES ";
    $name=$filename;
    $name=~s/report.php//;
    $query_p=0;
    open (WORKFILE, $filename) || warn "Can't open $filename: $!\n";
    while (<WORKFILE>) {
	if ($_ =~ /^EOD;/) {$query_p=0;}
	if ($query_p == 1) {$query.=$_;next;}
	if ($_ =~ /\$title="(.*)";/) {$title.=$1;}
	if ($_ =~ /\$description="(.*)";/) {$desc.=$1;}
	if ($_ =~ /\$additionalinfo="(.*)";/) {$addinfo.=$1;}
	if ($_ =~ /\$query.*=/) {$query_p=1;}
    }
    close(WORKFILE);
    chomp $query;
    $query=~s/'/\\'/g;
    $desc=~s/'/\\'/g;
    $addinfo=~s/'/\\'/g;
    $title=~s/'/\\'/g;
    $insert_string.="('".$name."','".$title."','".$desc."','".$addinfo."','".$query."');
";
    $name=$title=$desc=$addinfo=$query="";
}
print $insert_string;
