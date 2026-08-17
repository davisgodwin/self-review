import { Outlet } from 'react-router-dom';

export default function AuthLayout() {
  return (
    <div className="min-h-screen bg-midnight text-white flex flex-col justify-center items-center px-4 py-8 font-poppins">
      <div className="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-softGold tracking-wide">Self Review</h1>
          <p className="text-slate-400 text-xs mt-1 uppercase tracking-widest">One Hour With God</p>
        </div>
        <Outlet />
      </div>
    </div>
  );
}