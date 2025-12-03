import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/layout/Header';
import Hero from './components/home/Hero';
import Section2 from './components/home/Section2';
import Services from './components/home/Services';
import Stats from './components/home/Stats';
import Courses from './components/home/Courses';
import About from './components/home/About';
import Coaches from './components/home/Coaches';
import CTA from './components/home/CTA';
import Footer from './components/layout/Footer';
import BookSession from './components/booking/BookSession';
import Dashboard from './components/coach/Dashboard';

function HomePage() {
    return (
        <>
            <Hero />
            <Section2 />
            <Services />
            <Stats />
            <Courses />
            <About />
            <Coaches />
            <CTA />
        </>
    );
}

function App() {
    return (
        <Router>
            <div className="min-h-screen bg-white">
                <Routes>
                    <Route path="/" element={
                        <>
                            <Header />
                            <HomePage />
                            <Footer />
                        </>
                    } />
                    <Route path="/coaches/:id/book" element={<BookSession />} />
                    <Route path="/coach/dashboard" element={<Dashboard />} />
                </Routes>
            </div>
        </Router>
    );
}

export default App;
