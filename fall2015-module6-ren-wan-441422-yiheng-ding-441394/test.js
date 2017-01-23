Array.prototype.diff = function(a) {
    return this.filter(function(i) {return a.indexOf(i) < 0;});
};

////////////////////  
// Examples  
////////////////////

var r1 = [1,2,3,4,5,6].diff( [3,4,5,7] );  
console.log(r1);

// => [1, 2, 6]

var r2 = ["test1","test2","test3","test4", "test5"].diff(["test1", "test2","test3","test4"]);  
console.log(r2);

// => ["test5", "test6"]