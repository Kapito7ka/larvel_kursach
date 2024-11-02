import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.jsx';
import TextInput from '@/Components/TextInput';
import LoadingButton from '@/Components/LoadingButton';

export default function Create({ performances, halls }) {
    const { data, setData, post, errors, processing } = useForm({
        performance_id: '',
        hall_id: '',
        datetime: '',
        price: '',
    });

    const handleChange = (e) => {
        const key = e.target.name;
        const value = e.target.value;
        setData(key, value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('shows.store'));
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-3xl mx-auto bg-white rounded shadow overflow-hidden">
                <form onSubmit={handleSubmit}>
                    <div className="p-8 -mr-6 -mb-8 flex flex-wrap">
                        <div className="pr-6 pb-8 w-full lg:w-1/2">
                            <label className="block text-sm font-medium text-gray-700" htmlFor="performance_id">
                                Performance
                            </label>
                            <select
                                id="performance_id"
                                name="performance_id"
                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value={data.performance_id}
                                onChange={handleChange}
                            >
                                <option value="">Select Performance</option>
                                {performances.map((performance) => (
                                    <option key={performance.id} value={performance.id}>
                                        {performance.title}
                                    </option>
                                ))}
                            </select>
                            {errors.performance_id && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.performance_id}
                                </div>
                            )}
                        </div>

                        <div className="pr-6 pb-8 w-full lg:w-1/2">
                            <label className="block text-sm font-medium text-gray-700" htmlFor="hall_id">
                                Hall
                            </label>
                            <select
                                id="hall_id"
                                name="hall_id"
                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value={data.hall_id}
                                onChange={handleChange}
                            >
                                <option value="">Select Hall</option>
                                {halls.map((hall) => (
                                    <option key={hall.id} value={hall.id}>
                                        Hall {hall.hall_number}
                                    </option>
                                ))}
                            </select>
                            {errors.hall_id && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.hall_id}
                                </div>
                            )}
                        </div>

                        <div className="pr-6 pb-8 w-full lg:w-1/2">
                            <label className="block text-sm font-medium text-gray-700" htmlFor="datetime">
                                Date and Time
                            </label>
                            <input
                                id="datetime"
                                name="datetime"
                                type="datetime-local"
                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value={data.datetime}
                                onChange={handleChange}
                            />
                            {errors.datetime && (
                                <div className="text-red-600 text-sm mt-1">
                                    {errors.datetime}
                                </div>
                            )}
                        </div>

                        <TextInput
                            className="pr-6 pb-8 w-full lg:w-1/2"
                            label="Price"
                            name="price"
                            type="number"
                            errors={errors.price}
                            value={data.price}
                            onChange={handleChange}
                        />
                    </div>
                    <div className="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="btn-indigo ml-auto"
                        >
                            Create Show
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
