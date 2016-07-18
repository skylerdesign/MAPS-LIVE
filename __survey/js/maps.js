$( document ).ready(function() {
	
	// Survey Submit custom dialog box
	
	                   
	
   
	var substringMatcher = function(strs) {
	  return function findMatches(q, cb) {
	    var matches, substringRegex;
	
	    // an array that will be populated with substring matches
	    matches = [];
	
	    // regex used to determine if a string contains the substring `q`
	    substrRegex = new RegExp(q, 'i');
	
	    // iterate through the pool of strings and for any string that
	    // contains the substring `q`, add it to the `matches` array
	    $.each(strs, function(i, str) {
	      if (substrRegex.test(str)) {
	        matches.push(str);
	      }
	    });
	
	    cb(matches);
	  };
	};
	
	
	var statesList = [
		{"stateCode": "AL", "stateName": "Alabama"},
		{"stateCode": "AK", "stateName": "Alaska"},
		{"stateCode": "AZ", "stateName": "Arizona"},
		{"stateCode": "AR", "stateName": "Arkansas"},
	    {"stateCode": "CA", "stateName": "California"},
	    {"stateCode": "CO", "stateName": "Colorado"},
	    {"stateCode": "CT", "stateName": "Connecticut"},
	    {"stateCode": "DE", "stateName": "Delaware"},
	    {"stateCode": "FL", "stateName": "Florida"},
	    {"stateCode": "GA", "stateName": "Georgia"},
	    {"stateCode": "HI", "stateName": "Hawaii"},
	    {"stateCode": "ID", "stateName": "Idaho"},
	    {"stateCode": "IL", "stateName": "Illinois"},
	    {"stateCode": "IN", "stateName": "Indiana"},
	    {"stateCode": "IA", "stateName": "Iowa"},
	    {"stateCode": "KS", "stateName": "Kansas"},
	    {"stateCode": "KY", "stateName": "Kentucky"},
	    {"stateCode": "LA", "stateName": "Louisiana"},
	    {"stateCode": "ME", "stateName": "Maine"},
	    {"stateCode": "MD", "stateName": "Maryland"},
	    {"stateCode": "MA", "stateName": "Massachusetts"},
	    {"stateCode": "MI", "stateName": "Michigan"},
	    {"stateCode": "MN", "stateName": "Minnesota"},
	    {"stateCode": "MS", "stateName": "Mississippi"},
	    {"stateCode": "MO", "stateName": "Missouri"},
	    {"stateCode": "MT", "stateName": "Montana"},
	    {"stateCode": "NE", "stateName": "Nebraska"},
	    {"stateCode": "NV", "stateName": "Nevada"},
	    {"stateCode": "NH", "stateName": "New Hampshire"},
	    {"stateCode": "NJ", "stateName": "New Jersey"},
	    {"stateCode": "NM", "stateName": "New Mexico"},
	    {"stateCode": "NY", "stateName": "New York"},
	    {"stateCode": "NC", "stateName": "North Carolina"},
	    {"stateCode": "ND", "stateName": "North Dakota"},
	    {"stateCode": "OH", "stateName": "Ohio"},
	    {"stateCode": "OK", "stateName": "Oklahoma"},
	    {"stateCode": "OR", "stateName": "Oregon"},
	    {"stateCode": "PA", "stateName": "Pennsylvania"},
	    {"stateCode": "RI", "stateName": "Rhode Island"},
	    {"stateCode": "SC", "stateName": "South Carolina"},
	    {"stateCode": "SD", "stateName": "South Dakota"},
	    {"stateCode": "TN", "stateName": "Tennessee"},
	    {"stateCode": "TX", "stateName": "Texas"},
	    {"stateCode": "UT", "stateName": "Utah"},
	    {"stateCode": "VT", "stateName": "Vermont"},
	    {"stateCode": "VA", "stateName": "Virginia"},
	    {"stateCode": "WA", "stateName": "Washington"},
	    {"stateCode": "WV", "stateName": "West Virginia"},
	    {"stateCode": "WI", "stateName": "Wisconsin"},
	    {"stateCode": "WY", "stateName": "Wyoming"}];
	
	var states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
	  'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii',
	  'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
	  'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
	  'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire',
	  'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
	  'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island',
	  'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
	  'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
	];
	
	$('#state').typeahead({
	  hint: true,
	  highlight: true,
	  minLength: 1
	},
	{
	  name: 'states',
	  source: substringMatcher(states)
	});
	
	
	
	
	
});