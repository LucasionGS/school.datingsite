class AdTemplate {
  constructor(data: {
    title?: string | HTMLElement,
    pictureUrl?: string,
  }) {
    this.title      = data.title
    this.pictureUrl = data.pictureUrl;
  }
  public title?: string | HTMLElement;
  public pictureUrl?: string;

  /**
   * Create the ad element.
   */
  create() {
    let main = document.createElement("div");
    if (this.title) {
      if (typeof this.title == "string") {
        let h2 = document.createElement("h2");
        h2.innerText = this.title;
        main.appendChild(h2);
      }
      else if (this.title instanceof HTMLElement) {
        main.appendChild(this.title);
      }

      if (this.pictureUrl) {
        let img = document.createElement("img");
        img.src = this.pictureUrl;
        main.appendChild(img);
      }
    }
  }
}