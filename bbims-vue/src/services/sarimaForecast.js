// src/services/sarimaForecast.js

export default function sarimaForecast({
  data,
  resids,
  p, d, q, P, D, Q, s,
  steps,
  phi, Phi,
  theta, Theta
}) {
  const T = data.length;

  // 1) Build the w[t] array (differenced series)
  let w = new Array(T).fill(0);
  for (let t = 0; t < T; t++) {
    if (t - 1 >= 0 && t - s >= 0 && t - s - 1 >= 0) {
      w[t] = (data[t] - data[t - 1]) - (data[t - s] - data[t - s - 1]);
    } else {
      w[t] = 0;
    }
  }

  // Copies to extend with forecasts
  let wForecast = w.slice();
  let yForecast = data.slice();
  let e = resids.slice();

  for (let i = 0; i < steps; i++) {
    wForecast.push(0);
    yForecast.push(0);
    // future residuals = 0
    e.push(0);
  }

  // 2) Recursively compute future w[T], w[T+1], ...
  for (let i = 1; i <= steps; i++) {
    let tF = T + i - 1;
    let tFm1 = tF - 1;
    let tFs = tF - s;
    let tFs1 = tFs - 1;

    // Safely fetch needed historical terms
    let w_tminus1 = tFm1 >= 0 ? wForecast[tFm1] : 0;
    let w_tminusS = tFs >= 0 ? wForecast[tFs] : 0;
    let e_tminus1 = tFm1 >= 0 ? e[tFm1] : 0;
    let e_tminusS = tFs >= 0 ? e[tFs] : 0;

    // ARMA recursion for w_{T+i}
    let wNext = phi * w_tminus1
              + Phi * w_tminusS
              + theta * e_tminus1
              + Theta * e_tminusS;
    wForecast[tF] = wNext;

    // Invert differencing to get Y_{T+i}
    let y_tminus1 = tFm1 >= 0 ? yForecast[tFm1] : 0;
    let y_tminusS = tFs >= 0 ? yForecast[tFs] : 0;
    let y_tminusSminus1 = tFs1 >= 0 ? yForecast[tFs1] : 0;

    yForecast[tF] = wNext + y_tminus1 + y_tminusS - y_tminusSminus1;
  }

  // Return the forecasted points for the requested number of steps
  return yForecast.slice(T, T + steps);
}
