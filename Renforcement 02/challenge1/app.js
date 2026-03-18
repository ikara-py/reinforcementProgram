const songs = [
  {
    title: "Blinding Lights",
    artist: "The Weeknd",
    duration: 200,
    genre: "Pop",
  },
  { title: "Bohemian Rhapsody", artist: "Queen", duration: 354, genre: "Rock" },
  { title: "Shape of You", artist: "Ed Sheeran", duration: 233, genre: "Pop" },
  {
    title: "Smells Like Teen Spirit",
    artist: "Nirvana",
    duration: 301,
    genre: "Rock",
  },
  { title: "Take Five", artist: "Dave Brubeck", duration: 324, genre: "Jazz" },
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
  { title: "So What", artist: "Miles Davis", duration: 545, genre: "Jazz" },
  {
    title: "Rolling in the Deep",
    artist: "Adele",
    duration: 228,
    genre: "Pop",
  },
  { title: "Hotel California", artist: "Eagles", duration: 391, genre: "Rock" },
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
    const min = Math.floor(song.duration / 60);
    const sec = song.duration % 60;
    const formated = min + ":" + sec;
    song.duration = formated;
    arr.push(song);
  });
  return arr;
}

function calculateTotal(songs) {
  let total = 0;
  songs.forEach((song) => {
    total += song.duration;
  });
  const min = Math.floor(total / 60);
  const sec = total % 60;
  const formated = min + ":" + sec;
  return formated;
}

//! console.log(convertDuration(songs));
//! console.log(calculateTotal(songs));
//! console.log(filterByGenre(songs, 'Rock'));
//! showSongs(songs);
