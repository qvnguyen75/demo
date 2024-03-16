class Item {
    constructor(position_x, position_y) {
      this.position_x = position_x;
      this.position_y = position_y;
      this.visited = false;
      this.start = false;
      this.end = false;
    }
}

export { Item }