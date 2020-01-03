#!/bin/bash
tmp_dir=$(mktemp -d -t ci-XXXXXXXXXX)
echo $tmp_dir
 

cp -r ../com_membershiptaxreport/** $tmp_dir

pushd ./
cd $tmp_dir
zip -r com_membershiptaxreport.zip .
popd

cp $tmp_dir/*.zip ../

rm -rf $tmp_dir