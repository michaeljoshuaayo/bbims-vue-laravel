const ARIMA = require('arima');

async function forecastBloodDemand(data, steps) {
    // Initialize the ARIMA model with seasonal parameters
    const arima = new ARIMA({
        p: 1,
        d: 1,
        q: 1,
        P: 1,
        D: 1,
        Q: 1,
        s: 7 // assume a seasonal period of 7 days (weekly)
    }).train(data);

    // Forecast the next 'steps' values
    const [pred, errors] = arima.predict(steps);
    return pred;
}

export { forecastBloodDemand };