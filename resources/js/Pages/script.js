class Item {
    constructor(position_x, position_y) {
      this.position_x = position_x;
      this.position_y = position_y;
      this.visited = false;
      this.distance = Infinity;
      this.prevNode = null; 
      this.children = [];
      this.start = false;
      this.end = false;
    }
}

export { Item }