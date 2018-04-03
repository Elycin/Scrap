// @flow

import React from 'react'
import ReactDOM from 'react-dom'

import './bootstrap'

import Test from './components/test'

// We're simply doing this check because Flow's typing system is weird.
const element = document.getElementById('app')
element && ReactDOM.render(<Test />, element)
