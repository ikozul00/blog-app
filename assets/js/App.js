

import React from 'react';
import { createRoot } from 'react-dom/client';

const App = () => {
    console.log("Component rendered");
    return(<div>
            <p>"Neki moj tekst"</p>
        </div>
    )
}
const root = createRoot(document.getElementById('root'));
root.render(App);
//ReactDOM.render(<App />, document.getElementById('root'));
