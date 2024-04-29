<template>
    <div class="mx-5">
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
        <div>
            <div class="status">
                <h2>Strikes:</h2>
                <ul class="status">
                    <li v-for="strike in strikes" :key="strike">{{ strike.icon }}</li>
                </ul>
            </div>
        </div>
        <div class="container">
            <template v-if="level == 1">
                <div class="row text-center">
                    <div class="col-lg-3 d-flex flex-column align-items-center"
                        v-for="element in list1"
                        :key="element.order"
                    >
                        <div @click="element.fixed=! element.fixed" aria-hidden="true" class="card-box-1 card card-body">
                            {{ element.data.text }}
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <draggable
                        tag="div"
                        class="row text-center"
                        :list="list2"
                        @change="log"
                        v-bind="dragOptions"
                        :move="onMove"
                        @start="isDragging = true"
                        @end="isDragging = false"
                    >
                        <transition-group :name="'flip-list'">
                            <div class="col-lg-3 d-flex flex-column align-items-center"
                                v-for="element in list2"
                                :key="element.order"
                            >
                                <div @click="element.fixed=! element.fixed" aria-hidden="true" class="card-box-1 card card-body">
                                    {{ element.data.text }}
                                </div>
                            </div>
                        </transition-group>
                    </draggable>
                </div>
            </template>
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

const data1 = [
    {
        id: 1,
        text: "aa",
    },
    {
        id: 2,
        text: "bb",
    },
    {
        id: 3,
        text: "cc",
    },
    {
        id: 4,
        text: "dd",
    },
];

const data2 = [
    {
        id: 1,
        text: "aa",
    },
    {
        id: 2,
        text: "bb",
    },
    {
        id: 3,
        text: "cc",
    },
    {
        id: 4,
        text: "dd",
    },
];

import { VueDraggableNext } from "vue-draggable-next";
export default {
    name: "Matching Game",
    components: {
        draggable: VueDraggableNext,
    },
    data: () => {
        return {
            level: 1,
            list2: [],
            strikes: [...defaultStrikes],
            editable: true,
            isDragging: false,
            delayedDragging: false
        };
    },
    created() {
        this.shuffleCards();
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
        list1() {
            const filteredData = data1.map((data, index) => {
                return { data, order: index + 1, fixed: false };
            });

            return _.shuffle(filteredData);
        },
        numberSelection() {

        },
        badGuesses() {
            return this.strikes.filter(s => s.guess).map(s => s.guess)
        },
        strikeout() {
            return this.badGuesses.length >= allowedStrikes
        },
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
        shuffleCards() {
            const filteredData = data2.map((data, index) => {
                return { data, order: index + 1, fixed: false };
            })

            this.list2 = _.shuffle(filteredData);
        },
        submit() {
            const data1 = this.list1.map(data=>data.data);
            const data2 = this.list2.map(data=>data.data);

            if (_.isEqual(data1, data2)) {
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
    width: 200px;
    height: 200px;
}

.border-1 {
    margin-left: 500px;
    margin-right: 500px;
}

.card-box-2 {
    width: 250px;
    height: 250px;
}

.card-box-3 {
    width: 300px;
    height: 300px;
}

.option-card {
    width: 200px;
    height: 200px;
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
</style>
