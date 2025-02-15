const ARIMA = require('arima');

async function forecastBloodDemand(data, steps) {
    // initialize the ARIMA model with seasonal parameters
    const arima = new ARIMA({
        p: 1,
        d: 1,
        q: 1,
        P: 1,
        D: 1,
        Q: 1,
        s: 12 // assume a seasonal period of 12 months
    }).train(data);

    // forecast the next 'steps' values
    const [pred, errors] = arima.predict(steps);
    return pred;
}
// dummy data
const exampleData = [100, 120, 130, 110, 115, 125, 140, 135, 150, 160, 170, 180];
forecastBloodDemand(exampleData, 12).then(prediction => {
    console.log('Forecasted Blood Demand:', prediction);
});

module.exports = {
    forecastBloodDemand
};