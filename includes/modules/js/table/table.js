function Table(seats){
		for (var i=1 ; i<=9 ; i++){
			this.seat[i]=new Seat(i);
		}