function mergeObjects (objTo, objFrom) {
    for (var key in objFrom) {
    	objTo[key] = objFrom[key];
    }
	return objTo;
};