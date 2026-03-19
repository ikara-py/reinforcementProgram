const sales = [
  { month: "January", revenue: 42000, number_of_clients: 150, city: "Paris" },
  { month: "February", revenue: 48000, number_of_clients: 170, city: "Lyon" },
  { month: "March", revenue: 52000, number_of_clients: 180, city: "Paris" },
  { month: "April", revenue: 60000, number_of_clients: 200, city: "Lyon" },
  { month: "May", revenue: 58000, number_of_clients: 190, city: "Marseille" },
  { month: "June", revenue: 54000, number_of_clients: 160, city: "Paris" },
  { month: "July", revenue: 65000, number_of_clients: 220, city: "Lyon" },
  { month: "August", revenue: 47000, number_of_clients: 180, city: "Marseille" },
  { month: "September", revenue: 53000, number_of_clients: 175, city: "Paris" },
  { month: "October", revenue: 70000, number_of_clients: 250, city: "Lyon" },
  { month: "November", revenue: 45000, number_of_clients: 160, city: "Marseille" },
  { month: "December", revenue: 75000, number_of_clients: 270, city: "Paris" }
];

function totalRevenue(sales){
    let total = 0;
    sales.forEach(sale => {
        total += sale.revenue
    });
    return total;
}


function averageRevenue(sales){
    const arr = [];
    sales.forEach((sale) =>{
        let average = sale.revenue / sale.number_of_clients;
        let temp = [sale.month, average];
        arr.push(temp);
    });
    return arr;
}

function maxRevenue(sales, above){
    let arr = [];
    sales.forEach((sale)=>{
        if(sale.revenue >= above ){
            arr.push(sale)
        }
    
    });
    if(arr.length == 0){
        return `no result above ${above}` 
    }
    return arr;
}


function getSummary(sales){
    const result = Object.groupBy(sales, ({ city }) => city);
    Object.entries(result).forEach(([city, transactions]) => {
        const sum = transactions.reduce((accumulator, currentValue) => accumulator + currentValue.revenue, 0);
        const cSum = transactions.reduce((accumulator, currentValue) => accumulator + currentValue.number_of_clients, 0);
        console.log(`${city}: ${sum} => number of clients ${cSum}`) ;
    });
}


function findBestRevenue(sales){
    const maxSale = sales.reduce((max, current) => {
        return current.revenue > max.revenue ? current : max;
    });
    return maxSale;
}


function leastNumberOfClients(sales){
    const lowClients = sales.reduce((low, current) => {
        return current.number_of_clients < low.number_of_clients ? current : low;
    });
    return lowClients;
}
function descendingSort(sales){
    const sorted = sales.sort(function(a, b) {
        return parseFloat(b.revenue) - parseFloat(a.revenue);
    });
    return sorted;
}


//! getSummary(sales)
//! console.log( descendingSort(sales));
//! console.log(findBestRevenue(sales));
//! console.log(leastNumberOfClients(sales));
//! console.log(totalRevenue(sales));
//! console.log(maxRevenue(sales, 50000));
//! console.log(averageRevenue(sales));
