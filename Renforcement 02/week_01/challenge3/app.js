const reviews = [
  { username: "Alice", rating: 5, comment: "Amazing recipe! Loved it!", date: "2023-05-01" },
  { username: "Bob", rating: 4, comment: "Really good, but I would add a bit more spice.", date: "2023-05-02" },
  { username: "Charlie", rating: 2, comment: "Not what I expected, the flavor was off.", date: "2023-05-03" },
  { username: "David", rating: 5, comment: "Delicious, will definitely make again!", date: "2023-05-04" },
  { username: "Eve", rating: 3, comment: "It was okay, nothing special.", date: "2023-05-05" },
  { username: "Frank", rating: 1, comment: "I hated it, did not turn out well at all.", date: "2023-05-06" },
  { username: "Grace", rating: 4, comment: "Tasty, but could be improved with a bit of garlic.", date: "2023-05-07" },
  { username: "Hank", rating: 5, comment: "So good! Highly recommend.", date: "2023-05-08" },
  { username: "Ivy", rating: 3, comment: "It was fine, but not my favorite.", date: "2023-05-09" },
  { username: "Jack", rating: 2, comment: "Didn't like it, very bland.", date: "2023-05-10" },
  { username: "Kara", rating: 4, comment: "Great, but a little too salty for my taste.", date: "2023-05-11" },
  { username: "Leo", rating: 5, comment: "Excellent! I can't stop eating it.", date: "2023-05-12" },
  { username: "Mia", rating: 1, comment: "Terrible, will never make this again.", date: "2023-05-13" },
  { username: "Nina", rating: 5, comment: "Best recipe ever! Simple and tasty.", date: "2023-05-14" },
  { username: "Oscar", rating: 4, comment: "Really good, I just added a bit of lemon for extra flavor.", date: "2023-05-15" }
];


function averageRating(reviews){
    let av = Math.floor(reviews.reduce((a, b)=> a + b.rating, 0) / reviews.length);
    return av;
}


function positiveReviews(reviews){
    const results = reviews.filter((n) => n.rating >= 4);
    return results;
}


function negativeReviews(reviews){
    const results = reviews.filter((n) => n.rating <= 2);
    return results;
}


function sortByDate(reviews){
    const sorted = reviews.sort(function(a,b){
      return new Date(b.date) - new Date(a.date);
    });
    return sorted;
}

//! console.log(sortByDate(reviews))

//! console.log(positiveReviews(reviews))
//! console.log(negativeReviews(reviews))

//! console.log(averageRating(reviews))