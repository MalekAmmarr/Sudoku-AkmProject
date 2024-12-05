<template>
  <SudokuGame msg="welcome to game"/> <!-- Make sure the component is actually used here -->

</template>

<script>
//import HelloWorld from './components/HelloWorld.vue'
import SudokuGame from './components/SudokuGame.vue'

export default {
  name: 'App',
  components: {
    SudokuGame    
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
