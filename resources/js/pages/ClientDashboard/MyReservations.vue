<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

const reservations = ref([
    { id: 1, date: '2025-04-22', time: '14:00', room: 'Conference Room A', status: 'Confirmed' },
    { id: 2, date: '2025-04-25', time: '10:00', room: 'Meeting Room 3', status: 'Pending' },
    { id: 3, date: '2025-04-30', time: '16:00', room: 'Board Room', status: 'Completed' },
]);
</script>

<template>
    <h1 class="text-2xl font-bold mb-6">My Reservations</h1>
    <Card>
        <CardContent class="p-0">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Date</TableHead>
                        <TableHead>Time</TableHead>
                        <TableHead>Room</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="reservation in reservations" :key="reservation.id">
                        <TableCell>{{ reservation.date }}</TableCell>
                        <TableCell>{{ reservation.time }}</TableCell>
                        <TableCell>{{ reservation.room }}</TableCell>
                        <TableCell>
                            <span :class="{
                                'text-green-600': reservation.status === 'Confirmed',
                                'text-yellow-600': reservation.status === 'Pending',
                                'text-gray-600': reservation.status === 'Completed'
                            }">
                                {{ reservation.status }}
                            </span>
                        </TableCell>
                        <TableCell>
                            <Button variant="ghost" size="sm">View</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>