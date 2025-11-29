import React from 'react';
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

function App() {
    return (
        <div className="min-h-screen bg-white">
            <Header />
            <Hero />
            <Section2 />
            <Services />
            <Stats />
            <Courses />
            <About />
            <Coaches />
            <CTA />
            <Footer />
        </div>
    );
}

export default App;
