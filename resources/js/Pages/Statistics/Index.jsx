import React, { useState } from 'react';
import { usePage, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import LoadingButton from '@/Components/LoadingButton';
import DateInput from '@/Components/DateInput';

export default function Index() {
    const { performances = [], filters } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        start_date: filters.start_date || '',
        end_date: filters.end_date || '',
        performance_id: filters.performance_id || '',
    });

    const [statistics, setStatistics] = useState([]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setData(name, value);
    };

    const fetchStatistics = (e) => {
        e.preventDefault();
        post(route('statistics.fetch'), {
            onSuccess: (page) => {
                setStatistics(page.props.statistics);
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">
                <form onSubmit={fetchStatistics} className="p-8 space-y-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Performance</label>
                        <select
                            name="performance_id"
                            value={data.performance_id}
                            onChange={handleChange}
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">All Performances</option>
                            {Array.isArray(performances) && performances.length > 0 ? (
                                performances.map((performance) => (
                                    <option key={performance.id} value={performance.id}>
                                        {performance.title}
                                    </option>
                                ))
                            ) : (
                                <option disabled>No performances available</option>
                            )}
                        </select>
                    </div>
                    <DateInput
                        label="Start Date"
                        name="start_date"
                        value={data.start_date}
                        onChange={handleChange}
                        errors={errors.start_date}
                    />
                    <DateInput
                        label="End Date"
                        name="end_date"
                        value={data.end_date}
                        onChange={handleChange}
                        errors={errors.end_date}
                    />
                    <LoadingButton loading={processing} type="submit" className="btn-indigo w-full">
                        Fetch Statistics
                    </LoadingButton>
                </form>

                {statistics.length > 0 ? (
                    <div className="p-8">
                        <h2 className="text-xl font-semibold mb-4">Statistics</h2>
                        <table className="min-w-full bg-white">
                            <thead>
                            <tr>
                                <th className="border px-4 py-2">Performance Title</th>
                                <th className="border px-4 py-2">Sale Date</th>
                                <th className="border px-4 py-2">Tickets Sold</th>
                            </tr>
                            </thead>
                            <tbody>

                            {statistics.map((stat, index) => (
                                <tr key={index}>
                                    <td className="border px-4 py-2">{stat.performance_title}</td>
                                    <td className="border px-4 py-2">{stat.sale_date}</td>
                                    <td className="border px-4 py-2">{stat.tickets_sold}</td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="p-8 text-center text-gray-500">No statistics found for the selected criteria.</div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
