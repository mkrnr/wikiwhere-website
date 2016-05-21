//id is the div id in which the map will be placed, without a leading #
function generateHeatMap(id, jsonFile) {
  var data
  var countries = {}
  var fillCols = {}
  fillCols["defaultFill"] = '#808080'
  d3.json(jsonFile, function(dataset) {
  	data = dataset
  	//console.log(data)
  	data.forEach(function(d, i) {
  		//
  		fillCols[data[i].label] = data[i].color
  		//console.log(fillCols)
  		//create Label
  		tmp1 = data[i].label
  		//console.log(tmp1)
  		//create fillKey
  		tmp2 = {fillKey: data[i].label, count: data[i].count}
  		//console.log(tmp2)
  		//add label : {fillKey: key} to object
  		countries[tmp1] = tmp2
  		//console.log(countries)
  	});


     //creates map with generated fill and data
  	var map = new Datamap({
  		scope: 'world',
  		element: document.getElementById(id),
  		projection: 'mercator',
      responsive: true,
  		fills: fillCols,
  		data: countries,
  		geographyConfig: {
  			popupTemplate: function(geo, data) {
  			 var count
  			 if (data == null) {count = 0}
  			 else {count = data.count}
  				return ['<div class="hoverinfo"><strong>',
  						'Links from ' + geo.properties.name,
  						': ' + count,
  						'</strong></div>'].join('');
        }
  		}
  	});

    window.addEventListener('resize', function() {
      map.resize();
    });
  });
}
