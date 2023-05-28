<template>
  <body>
    <canvas ref="canvas"></canvas>
  </body>
</template>

<script>
export default {
  data() {
    return {
      array: [],
      y: -10,
      k: 0,
      h: 0,
      ct: 8,
      len: 0,
      scr: 0,
      count: 0,
      gameIsOver: false,
      isExit: false,
      selectColor: [
        "#DB291D",
        "#F85F68",
        "#77B1AD",
        "#F0B99A",
        "#E1CF79",
        "#F5998E",
        "#B16774",
        "#FAB301",
        "#F85F68",
        "#4FAA6D",
        "orange",
        "green",
        "#58A8C9",
        "#CDD6D5",
        "#ECD2A2",
        "white"
      ],
      theme: [ 
        { letter: 'a', image: 'images/signs/A.png' },
        { letter: 'b', image: 'images/signs/B.png' },
        { letter: 'c', image: 'images/signs/C.png' },
      ],
      c: "",
      ctx: ""
    };
  },
  computed: {
    menuFlag() {
        return this.$store.state.menuFlag;
    },
    isQuiz() {
        return window.localStorage.getItem('menuFlag') == "quiz" ? true : false
    }
  },
  mounted() {
    this.c = this.$refs.canvas;
    this.ctx = this.c.getContext("2d");
    this.c.height = window.innerHeight * 0.98;
    this.c.width = window.innerWidth * 0.996;
    this.initBalloons();
    this.animate();
    window.addEventListener("keyup", this.handleKeyUp);
  },
  beforeUnmount() {
    window.removeEventListener("beforeunload", this.isExit = true);
  },
  methods: {
    initBalloons() {
      this.theme = _.shuffle(this.theme)
      for (let i = 0; i < this.theme.length; i++) {
        let x = Math.random() * (this.c.width - 60) + 100;
        this.y -= 200;
        let r = 100;
        let dy = 1;
        let color =
          this.selectColor[
          Math.floor(Math.random() * (this.selectColor.length - 1))
          ];
        let url = this.theme[i].image;
        let text = this.theme[i].letter;
        this.array.push({
          x: x,
          y: this.y,
          r: r,
          color: color,
          dy: dy,
          text: text,
          url: url,
          word: ""
        });
      }
    },
    animate() {
      if(this.isExit) {
        this.ctx.clearRect(0, 0, innerWidth, innerHeight);
        this.$router.push('/student-dashboard')
        window.location.reload()
        return
      } 
      requestAnimationFrame(this.animate);
      this.ctx.clearRect(0, 0, innerWidth, innerHeight);
      this.ctx.font = "18px Arial";
      this.ctx.fillStyle = "black";
      this.ctx.fillText("SCORE:", 1200, 20);

      this.ctx.fillText(this.scr, 1280, 20);
      this.ctx.font = "40px Arial";
      this.ctx.strokeStyle = "lightgrey";
      this.ctx.strokeText("Vowel/Consonants", 10, 40);
      this.ctx.beginPath();
      this.ctx.fillStyle = "lightgrey";
      this.ctx.fillRect(
        this.c.width / 2 - 20,
        this.c.height - 50,
        50,
        50
      );
      this.ctx.fill();
      for (let m = 0; m < this.array.length; m++) {
        this.drawBalloon(this.array[m]);
      }
      for (let p = 0; p < this.array.length; p++) {
        this.updateBalloon(this.array[p]);
        if (this.array[p].y >= this.c.height) {
          if (this.array[p].r > 0) {
            this.count++
            if (this.count == 1) {
              alert("Game over!!");
              this.$router.push('/student-dashboard')
            }
          }
        }
      }
    },
    drawBalloon(balloon) {
      this.ctx.beginPath();
      this.ctx.arc(balloon.x, balloon.y, balloon.r, 0, 2 * Math.PI);
      this.ctx.fillStyle = balloon.color;
      this.ctx.fill();
      this.ctx.stroke();
      this.ctx.font = "15px Arial";
      this.ctx.fillStyle = "white";
      let img = new Image()
      img.src = balloon.url;
      this.ctx.drawImage(img, balloon.x - 55, balloon.y - 75, 110, 160);
    },
    updateBalloon(balloon) {
      this.typeBalloon(balloon);
      balloon.y += balloon.dy;
    },
    typeBalloon(balloon) {
      this.ctx.font = "40px Arial";
      this.ctx.strokeStyle = "lightgrey";
      this.ctx.strokeText(balloon.word, 670, 580);
    },
    handleKeyUp(event) {
      let char = String.fromCharCode(event.keyCode).toLowerCase();
      if (this.ct == 8) {
        for (let g = 0; g < this.array.length; g++) {
          if (
            this.array[g].y >= 0 &&
            this.array[g].text.substring(0, 1) === char
          ) {
            this.h = g;
            this.len = this.array[this.h].text.length;
            break;
          }
        }
      }
      if (this.array[this.h].text.substring(0, 1) === char) {
        let word = this.array[this.h].text.substring(0, 1);
        this.array[this.h].word = word;
        this.array[this.h].text = this.array[this.h].text.replace(word, "");
        this.ct--;
        if (this.array[this.h].text.length == 0) {
          this.ct += this.len;
          this.array[this.h].r = 0;
          this.array[this.h].y = 0;
          this.scr++;
          this.array[this.h].word = "";
          this.array[this.h].url = '';
        }
        if (this.scr == this.array.length) {
          alert("You win the game!");
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
#canvas {
  border: 1px solid white;
}

body {
  margin: 0px;
}
</style>