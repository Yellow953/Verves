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
import ForumHome from './components/forum/ForumHome';
import CategoryView from './components/forum/CategoryView';
import TopicView from './components/forum/TopicView';
import CoachesPage from './components/pages/CoachesPage';
import ServicesPage from './components/pages/ServicesPage';
import Chatbot from './components/chatbot/Chatbot';

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
                    <Route path="/coaches" element={
                        <>
                            <Header />
                            <CoachesPage />
                            <Footer />
                        </>
                    } />
                    <Route path="/coaches/:id/book" element={<BookSession />} />
                    <Route path="/services" element={
                        <>
                            <Header />
                            <ServicesPage />
                            <Footer />
                        </>
                    } />
                    <Route path="/coach/dashboard" element={<Dashboard />} />
                    <Route path="/forum" element={
                        <>
                            <Header />
                            <ForumHome />
                            <Footer />
                        </>
                    } />
                    <Route path="/forum/category/:id" element={
                        <>
                            <Header />
                            <CategoryView />
                            <Footer />
                        </>
                    } />
                    <Route path="/forum/topic/:id" element={
                        <>
                            <Header />
                            <TopicView />
                            <Footer />
                        </>
                    } />
                </Routes>
                <Chatbot />
            </div>
        </Router>
    );
}

export default App;
