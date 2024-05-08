<template>
    <div v-if="isLoading">Loading...</div>
    <div v-else class="mx-5">
        <div class="row w-100 my-5">
            <div class="col-6">
                <h4 class="text-start fw-bold ms-4">Highest Score: {{ 1 }}
                </h4>
            </div>
            <div class="col-6">
                <h4 class="text-end fw-bold me-4">Score: {{ 1 }}
                </h4>
            </div>
        </div>
        <div :class="optionClasses.container_class">
            <div class="mb-5">
                <div class="status">
                    <h2 class="fw-bold">Strikes:</h2>
                    <ul class="status">
                        <li v-for="strike in strikes" :key="strike">{{ strike.icon }}</li>
                    </ul>
                </div>
            </div>
            <div :class="optionClasses.row_class || ''" class="row">
                <div :class="optionClasses.grid_class" class="d-flex flex-column align-items-center"
                    v-for="element in matchA"
                    :key="element.order"
                >
                    <div :class="optionClasses.card_class" aria-hidden="true" class="card card-body d-flex flex-row justify-content-center">
                        <img :class="optionClasses.image_class" :src="element.data.image" alt="Options" srcset="">
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <draggable
                    tag="div"
                    :class="optionClasses.row_class"
                    class="row"
                    :list="matchB"
                    @change="log"
                    v-bind="dragOptions"
                    :move="onMove"
                    @start="isDragging = true"
                    @end="isDragging = false"
                >
                    <transition-group :name="'flip-list'">
                        <div :class="optionClasses.grid_class" class="d-flex flex-column align-items-center"
                            v-for="element in matchB"
                            :key="element.order"
                        >
                            <div :class="optionClasses.card_class" style="cursor: pointer" @click="element.fixed =! element.fixed" aria-hidden="true" class="card card-body d-flex flex-row justify-content-center">
                                <img :class="optionClasses.image_class" :src="element.data.image" alt="Options" srcset="">
                            </div>
                        </div>
                    </transition-group>
                </draggable>
            </div>
        </div>
        <div class="submit-button text-center mt-5">
            <button type="button" class="btn btn-lg rounded-0 btn-secondary" @click="submit">Submit</button>
        </div>
    </div>
</template>

<script>
//Change this if you want more or fewer strikes allowed
const allowedStrikes = 3 //If you set this and maxLength both too high, the puzzle will be impossible to lose.

const defaultStrikes = [];

for (let i = 0; i < allowedStrikes; i++) {
    const key = Math.floor(Math.random() * 100); // Generate a random number between 0 and 99

    defaultStrikes.push({
        key: key,
        icon: '⚪',
        guess: ''
    });
}

import { VueDraggableNext } from "vue-draggable-next";
export default {
    name: "Matching Game",
    components: {
        draggable: VueDraggableNext,
    },
    data: () => {
        return {
            level: 3,
            list2: [],
            strikes: [...defaultStrikes],
            editable: true,
            isDragging: false,
            delayedDragging: false,
            randomNumber: null,
            optionA: [],
            optionB: [],
            matchA: [],
            matchB: [],
            isLoading: false
        };
    },
    async created() {
        this.isLoading = true
        await this.fetchMatchOptions();
        await this.generateRandomNumber();
        await this.generateOptions()
    },
    computed: {
        dragOptions() {
            return {
                animation: 0,
                group: "description",
                disabled: !this.editable,
                ghostClass: "ghost"
            };
        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
        arrayNumber() {
            if (this.randomNumber) {
                let myFunc = num => Number(num);

                return Array.from(String(this.randomNumber), myFunc);
            }

            return [];
        },
        optionClasses() {
            let rowClass = null;
            let cardClass = null;
            let imageClass = null;
            let gridClass = null;
            let containerClass = null;

            if (this.level == 1) {
                gridClass = 'col-3';
                containerClass = 'game-container-1';
                cardClass = 'card-box-1';
                imageClass = 'option-image-1';
                rowClass = 'd-flex justify-content-center';
            } else if (this.level == 2) {
                gridClass = 'col-1 me-4';
                imageClass = 'option-image-2';
                cardClass = 'card-box-2';
                rowClass = 'd-flex justify-content-center';
                containerClass = 'game-container-2';
            } else {
                gridClass = 'col-1';
                imageClass = 'option-image-3';
                cardClass = 'card-box-3';
                rowClass = 'd-flex justify-content-center';
                containerClass = 'game-container-3';
            }

            return {
                row_class: rowClass,
                card_class: cardClass,
                image_class: imageClass,
                grid_class: gridClass,
                container_class: containerClass
            }
        }
    },
    watch: {
        isDragging(newValue) {
            if (newValue) {
                this.delayedDragging = true;
                return;
            }

            this.$nextTick(() => {
                this.delayedDragging = false;
            });
        }
    },
    methods: {
        log(event) {
            console.log(event);
        },
        onMove({ relatedContext, draggedContext }) {
            const relatedElement = relatedContext.element;
            const draggedElement = draggedContext.element;
            return (
                (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
            );
        },
        async fetchMatchOptions() {
            try {
                const { data } = await axios.get('json/games/matching-game.json');

                this.optionA = data.matchA;
                this.optionB = data.matchB;
            } catch (error) {
                console.log(error)
            }
        },
        async generateRandomNumber() {
            let min;
            let max;

            switch (this.level) {
                case 1:
                    min = 1000;
                    max = 9999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;

                case 2:
                    min = 10000000;
                    max = 99999999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;

                default:
                    min = 100000000000;
                    max = 999999999999;
                    this.randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                    break;
            }
        },
        async generateOptions() {
            try {
                const data1 = await this.filterMatch('a');
                const data2 = await this.filterMatch('b');

                this.matchA = data1;
                this.matchB = data2;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false;
            }
        },
        async filterMatch(option) {
            let options;

            switch (option) {
                case 'a':
                    options = this.optionA;
                    break;

                default:
                    options = this.optionB;
                    break;
            }

            if (this.arrayNumber.length) {
                const filteredData = this.arrayNumber.map((number, index) => {
                    const foundOption = options.find(option => parseInt(option.number) == number)

                    return { data: foundOption, order: index + 1, fixed: false };
                });

                return _.shuffle(filteredData);
            }

            return [];
        },
        submit() {
            const matchA = this.matchA.map(data=>data.data.number);
            const matchB = this.matchB.map(data=>data.data.number);

            if (_.isEqual(JSON.stringify(matchA), JSON.stringify(matchB))) {
                alert('Equal');
            } else {
                alert('Not Equal');
            }
        }
    },
};
</script>

<style lang="scss" scoped>
.card-box-1 {
    width: 150px;
    height: 150px;
}

.card-box-2 {
    width: 120px;
    height: 120px;
}

.card-box-3 {
    width: 100px;
    height: 100px;
}

.flip-list-move {
    transition: transform 0.5s;
}
.no-move {
    transition: transform 0s;
}
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}
.status {
    display: flex;
    flex-wrap: wrap;
    list-style-type: none;
    align-items: center;
    margin: 1rem 0;

    h2 {
        font-size: 1rem;
        margin: 0;
    }

    ul {
        display: flex;
        margin: 0;
        padding: 0;

        li {
            margin-left: 0.25em;
        }
    }

    p {
        font-size: 1rem;
        width: 100%;
        margin: 0;
    }
}

.game-container-1 {
    margin-left: 300px;
    margin-right: 300px;
}

.game-container-2 {
    margin-left: 0px;
    margin-right: 0px;
}

.game-container-3 {
    margin-left: 0px;
    margin-right: 0px;
}

.option-image-1 {
    width: 110px
}

.option-image-2 {
    width: 80px
}

.option-image-3 {
    width: 50px
}
</style>
