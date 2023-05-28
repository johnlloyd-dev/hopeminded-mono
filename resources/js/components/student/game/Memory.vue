<template>
  <div>
    <h4 class="text-center">Score: {{ score }}</h4>
    <div id="app">
      <p class="victoryState" v-if="victory">Done! <button v-on:click="cardsShuffle" class="btn btn-primary">Refresh
          ?</button></p>
      <div v-for="(card, index) in cards" :key="index"
        :class="[{ 'down': card.down && !card.matched, 'up': !card.down, 'matched': card.matched }, ' card']"
        v-on:click="handleClick(card)">
        <img :src="'/images/letters/' + card.icon + '.JPG'"/>
      </div>
    </div>
  </div>
</template>

<script>
// List of font awesome codes used as illustrations, can be modified
const icons = [
  'A',
  'B',
  'C',
  // 'D',
  // 'E',
  // 'F',
  // 'G',
  // 'H',
  // 'I',
  // 'J',
  // 'K',
  // 'L',
  // 'M',
  // 'N',
  // 'O',
  // 'P',
  // 'Q',
  // 'R',
  // 'S',
  // 'T',
  // 'U',
  // 'V',
  // 'W',
  // 'X',
  // 'Y',
];

// Duplicate elements of an array
const duplicate = (arr) => {
  return arr.concat(arr).sort()
};

// Check if two cards are a match
const checkMatch = (icons) => {
  if (icons[0] === icons[1]) {
    console.log("it's a match");
    return true;
  }
};

export default {
  data() {
    return {
      cards: _.range(0, icons.length * 2),
      runing: false,
      score: 0
    }
  },
  methods: {
    // Create cards array based on icons, shuffle them
    cardsShuffle() {
      // prep objects
      this.score = 0
      this.cards.forEach((card, index) => {
        this.cards[index] = {
          icon: '',
          down: true,
          matched: false
        }
      });
      // input every icon two times
      icons.forEach((icon, index) => {
        this.cards[index].icon = icon;
        this.cards[index + icons.length].icon = icon;
      });
      this.cards = _.shuffle(this.cards);
    },
    handleClick(cardClicked) {
      if (!this.runing) {
        // turn card up
        if (!cardClicked.matched && this.cardCount.cardsUp < 2) {
          cardClicked.down = false;
        }
        // when two cards are up, check if they match or turn them down
        if (this.cardCount.cardsUp === 2) {
          this.runing = true;
          setTimeout(() => {
            let match = checkMatch(this.cardCount.icons);
            this.cards.forEach((card) => {
              if (match && !card.down && !card.matched) {
                card.matched = true;
                this.score++
              } else {
                card.down = true;
              }
            });
            this.runing = false;
          }, 1000);
        }
      }
    }
  },
  mounted() {
    this.cardsShuffle();
  },
  computed: {
    // make a count of cards up and cards matched, keep icons of cards to check in array
    cardCount: function () {
      let cardUpCount = 0;
      let cardMatchedCount = 0;
      let icons = [];
      this.cards.forEach((card) => {
        if (!card.down && !card.matched) {
          cardUpCount++;
          icons.push(card.icon);
        }
        if (card.matched) {
          cardMatchedCount++;
        }
      });
      return {
        cardsUp: cardUpCount,
        cardsMatched: cardMatchedCount,
        icons: icons
      }
    },
    // update victory state
    victory: function () {
      if (this.cardCount.cardsMatched === this.cards.length) {
        return true;
      }
    }
  }
}
</script>

<style lang="scss" scoped>
body {
  background: oldlace;
  padding: 20px;
  font-family: Helvetica, Arial, Sans-Serif;
}

#app {
  display: grid;
  grid-template-columns: repeat(auto-fit, 100px);
  grid-auto-rows: 100px;
  grid-gap: 15px;
  justify-content: center;
  perspective: 800px;
  max-width: 1230px;
  margin: 0 auto;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

img {
  width: 100%;
}

.victoryState {
  grid-column-start: 1;
  grid-column-end: -1;
  text-align: center;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card {
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 5px;
  box-shadow: 5px 5px 0 0 #333;
  cursor: pointer;
  animation: flipUp .5s forwards;

  img {
    opacity: 1;
    transition: opacity .5s;
  }

  &.down {
    animation: flipDown .5s forwards;

    img {
      opacity: 0;
    }
  }

  &.matched {
    animation: matching .3s forwards;
    animation-name: pop;
    animation-duration: 0.3s;
    animation-timing-function: ease-out;
  }
}

@keyframes flipDown {
  0% {
    background: #fff;
    transform: rotateY(0deg);
    box-shadow: 5px 5px 0 0 #333;
  }

  100% {
    background: rgb(115, 132, 127);
    transform: rotateY(180deg);
    box-shadow: -5px 5px 0 0 #333;
  }
}

@keyframes flipUp {
  0% {
    background: rgb(115, 132, 127);
    transform: rotateY(180deg);
    box-shadow: -5px 5px 0 0 #333;
  }

  100% {
    background: #fff;
    transform: rotateY(0deg);
    box-shadow: 5px 5px 0 0 #333;
  }
}

@keyframes matching {
  0% {
    transform: scale(1);
    background: #ffffff;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    box-shadow: 5px 5px 0 0 lightpink;
  }
}

@keyframes pop {
  0% {
    transform: scale(1);
    background: #ffffff;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    box-shadow: 5px 5px 0 0 lightpink;
  }
}

.text-center {
  text-align: center;
  margin-top: 20px;
  font-weight: bold;
}
</style>