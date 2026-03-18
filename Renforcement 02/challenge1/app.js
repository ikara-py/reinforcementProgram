const songs = [
  {
    title: "Blinding Lights",
    artist: "The Weeknd",
    duration: 200,
    genre: "Pop",
  },
  { title: "Bohemian Rhapsody",
    artist: "Queen",
    duration: 354, 
    genre: "Rock" },
  { title: "Shape of You",
    artist: "Ed Sheeran",
    duration: 233, 
    genre: "Pop" },
  {
    title: "Smells Like Teen Spirit",
    artist: "Nirvana",
    duration: 301,
    genre: "Rock",
  },
  { title: "Take Five", 
    artist: "Dave Brubeck", 
    duration: 324, 
    genre: "Jazz" },
  {
    title: "Billie Jean",
    artist: "Michael Jackson",
    duration: 294,
    genre: "Pop",
  },
  {
    title: "Stairway to Heaven",
    artist: "Led Zeppelin",
    duration: 482,
    genre: "Rock",
  },
  { title: "So What", 
    artist: "Miles Davis", 
    duration: 545, 
    genre: "Jazz" },
  {
    title: "Rolling in the Deep",
    artist: "Adele",
    duration: 228,
    genre: "Pop",
  },
  { title: "Hotel California", 
    artist: "Eagles", 
    duration: 391, 
    genre: "Rock" },
];

function showSongs(songs) {
  songs.forEach((song) => {
    console.log(song.title);
  });
}

function filterByGenre(songs, genre) {
  const filtred = songs.filter((song) => song.genre == genre);
  return filtred;
}

function convertDuration(songs) {
  const arr = [];
  songs.forEach((song) => {
    song.duration = convert(song.duration);
    arr.push(song);
  });
  return arr;
}

function calculate(minute){
  const min = Math.floor(minute / 60);
  return min
}

function convert(minute) {
  const sec = minute % 60;
  const formated = calculate(minute) + ":" + sec;
  return formated;
}

function calculateTotal(songs) {
  let total = 0;
  songs.forEach((song) => {
    total += song.duration;
  });
  return convert(total);
}

function getLongestSong(songs) {
  const longest = songs.reduce((prevSong, current) => {
    return prevSong.duration > current.duration ? prevSong : current;
  });
  longest.duration = convert(longest.duration);
  return longest;
}

function checkDuration(songs, maxMin){
  let arr = [];
  songs.forEach((song)=>{
    if (calculate(song.duration) <= maxMin) {
      arr.push(song)
    }
  })
  if (arr.length == 0) {
    return `no songs are under or equal ${maxMin} `
  }
  return arr
}


function getLatestSong(songs, genre){
  let arr = [];
  songs.forEach((song) =>{
    if (song.genre == genre) {
      arr.push(song)
    }
  })
  if(arr.length == 0){
    return 'there is no song with that genre'
  }
  return arr[arr.length - 1]
}

function sortSongsByDuration(songs){
  const safeCopy = [... songs]
  return safeCopy.sort(function(a, b) {
    return a.duration - b.duration ;
  });
}


console.log(sortSongsByDuration(songs));
//! console.log(songs)
//! console.log(getLatestSong(songs, 'Jazz'))
//! console.log(convert(222));
//! console.log(checkDuration(songs, 6));
//! console.log(getLongestSong(songs));
//! console.log(convertDuration(songs));
//! console.log(calculateTotal(songs));
//! console.log(filterByGenre(songs, 'Rock'));
//! showSongs(songs);
