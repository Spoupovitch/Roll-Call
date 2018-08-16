var mysql = require('mysql');
var express = require('express');

//create mysql connection
var conn = mysql.createConnection({
	host: 'localhost',
	user: 'root',
	password: '',
	database: 'roster_db'
});

db.connect((err) => {
	if (err) {
		throw err;
	}
	console.log('Node MySQL connected successfully');
});

//drop table
express.get('/drop', (table) => {
	let sql = 'DROP TABLE ?';
	let query = db.query(sql, table, (err, res) => {
		if (err) {
			throw err;
		}
		console.log(res);
		res.send('Table dropped');
	});
});

function placeholder() {
	alert('Coming Soon');
}