<p>Sort Table Rows by Clicking on the Table Headers - Ascending and Descending (jQuery)</p>
<div class="container">
	
	<div class="table">
		<div class="table-header">
			<div class="header__item"><a id="name" class="filter__link" href="#">Name</a></div>
			<div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">Wins</a></div>
			<div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">Draws</a></div>
			<div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">Losses</a></div>
			<div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">Total</a></div>
		</div>
		<div class="table-content">	
			<div class="table-row">		
				<div class="table-data">Tom</div>
				<div class="table-data">2</div>
				<div class="table-data">0</div>
				<div class="table-data">1</div>
				<div class="table-data">5</div>
			</div>
			<div class="table-row">
				<div class="table-data">Dick</div>
				<div class="table-data">1</div>
				<div class="table-data">1</div>
				<div class="table-data">2</div>
				<div class="table-data">3</div>
			</div>
			<div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div>
		</div>	
	</div>
</div>

@import url('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700');

$base-spacing-unit: 24px;
$half-spacing-unit: $base-spacing-unit / 2;

$color-alpha: #1772FF;
$color-form-highlight: #EEEEEE;

*, *:before, *:after {
	box-sizing:border-box;
}

body {
	padding:$base-spacing-unit;
	font-family:'Source Sans Pro', sans-serif;
	margin:0;
}

h1,h2,h3,h4,h5,h6 {
	margin:0;
}

.container {
	max-width: 1000px;
	margin-right:auto;
	margin-left:auto;
	display:flex;
	justify-content:center;
	align-items:center;
	min-height:100vh;
}

.table {
	width:100%;
	border:1px solid $color-form-highlight;
}

.table-header {
	display:flex;
	width:100%;
	background:#000;
	padding:($half-spacing-unit * 1.5) 0;
}

.table-row {
	display:flex;
	width:100%;
	padding:($half-spacing-unit * 1.5) 0;
	
	&:nth-of-type(odd) {
		background:$color-form-highlight;
	}
}

.table-data, .header__item {
	flex: 1 1 20%;
	text-align:center;
}

.header__item {
	text-transform:uppercase;
}

.filter__link {
	color:white;
	text-decoration: none;
	position:relative;
	display:inline-block;
	padding-left:$base-spacing-unit;
	padding-right:$base-spacing-unit;
	
	&::after {
		content:'';
		position:absolute;
		right:-($half-spacing-unit * 1.5);
		color:white;
		font-size:$half-spacing-unit;
		top: 50%;
		transform: translateY(-50%);
	}
	
	&.desc::after {
		content: '(desc)';
	}

	&.asc::after {
		content: '(asc)';
	}
	
}

var properties = [
	'name',
	'wins',
	'draws',
	'losses',
	'total',
];

$.each( properties, function( i, val ) {
	
	var orderClass = '';

	$("#" + val).click(function(e){
		e.preventDefault();
		$('.filter__link.filter__link--active').not(this).removeClass('filter__link--active');
  		$(this).toggleClass('filter__link--active');
   		$('.filter__link').removeClass('asc desc');

   		if(orderClass == 'desc' || orderClass == '') {
    			$(this).addClass('asc');
    			orderClass = 'asc';
       	} else {
       		$(this).addClass('desc');
       		orderClass = 'desc';
       	}

		var parent = $(this).closest('.header__item');
    		var index = $(".header__item").index(parent);
		var $table = $('.table-content');
		var rows = $table.find('.table-row').get();
		var isSelected = $(this).hasClass('filter__link--active');
		var isNumber = $(this).hasClass('filter__link--number');
			
		rows.sort(function(a, b){

			var x = $(a).find('.table-data').eq(index).text();
    			var y = $(b).find('.table-data').eq(index).text();
				
			if(isNumber == true) {
    					
				if(isSelected) {
					return x - y;
				} else {
					return y - x;
				}

			} else {
			
				if(isSelected) {		
					if(x < y) return -1;
					if(x > y) return 1;
					return 0;
				} else {
					if(x > y) return -1;
					if(x < y) return 1;
					return 0;
				}
			}
    		});

		$.each(rows, function(index,row) {
			$table.append(row);
		});

		return false;
	});

});