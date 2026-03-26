<script setup lang="ts">
import { VisXYContainer, VisLine, VisAxis, VisCrosshair, VisTooltip, VisStackedBar, VisScatter } from '@unovis/vue';
import { Scale } from '@unovis/ts';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { computed } from 'vue';

const props = defineProps<{
    data: Array<{
        day: string;
        revenue: number;
        appointments: number;
    }>;
}>();

const chartData = computed(() => {
    return props.data.map(d => ({
        x: new Date(d.day).getTime(),
        revenue: Number(d.revenue),
        appointments: Number(d.appointments)
    }));
});

// Accessors
const x = (d: any) => d.x;
const yRevenue = (d: any) => d.revenue;
const yAppointments = (d: any) => d.appointments;

// Scales for Dual Axis
const revenueScale = computed(() => {
    const max = Math.max(...chartData.value.map(d => d.revenue), 100);
    return Scale.scaleLinear().domain([0, max * 1.1]);
});

const appointmentsScale = computed(() => {
    const max = Math.max(...chartData.value.map(d => d.appointments), 5);
    return Scale.scaleLinear().domain([0, max + 1]);
});

const formatDate = (timestamp: number) => {
    return new Date(timestamp).toLocaleDateString('pl-PL', { day: 'numeric', month: 'short' });
};

const formatRevenue = (value: number) => `${value} zł`;
const formatCount = (value: number) => Math.round(value).toString();
</script>

<template>
    <Card class="w-full border-none shadow-none bg-transparent">
        <CardHeader class="pb-2">
            <CardTitle class="text-lg font-bold">Wydajność miesięczna</CardTitle>
            <CardDescription class="text-xs">Przychody (lewa oś) vs Liczba wizyt (prawa oś).</CardDescription>
        </CardHeader>
        <CardContent>
            <div class="h-[350px] w-full mt-4">
                <VisXYContainer :data="chartData" :margin="{ top: 20, right: 50, bottom: 20, left: 60 }">
                    <!-- Revenue Bars -->
                    <VisStackedBar 
                        :x="x" 
                        :y="yRevenue" 
                        :yScale="revenueScale"
                        color="oklch(0.627 0.265 303.9)" 
                        :barRadius="4" 
                    />
                    
                    <!-- Appointments Line -->
                    <VisLine 
                        :x="x" 
                        :y="yAppointments" 
                        :yScale="appointmentsScale"
                        color="oklch(0.446 0.043 257.281)" 
                        :lineWidth="3" 
                    />
                    <VisScatter 
                        :x="x" 
                        :y="yAppointments" 
                        :yScale="appointmentsScale"
                        color="oklch(0.446 0.043 257.281)" 
                        :size="8" 
                    />

                    <!-- Axes -->
                    <VisAxis type="x" :tickFormat="formatDate" :gridLine="false" />
                    <VisAxis 
                        type="y" 
                        :scale="revenueScale"
                        :tickFormat="formatRevenue" 
                        :gridLine="true" 
                        :label="'Przychód'" 
                    />
                    <VisAxis 
                        type="y" 
                        position="right" 
                        :scale="appointmentsScale"
                        :tickFormat="formatCount" 
                        :gridLine="false" 
                        :label="'Wizyty'" 
                    />
                    
                    <VisCrosshair :template="(d: any) => `${formatDate(d.x)}: ${d.revenue} zł | ${d.appointments} wizyt`" />
                    <VisTooltip />
                </VisXYContainer>
            </div>
            
            <div class="flex items-center justify-center gap-10 mt-6 text-[10px] font-black uppercase tracking-widest text-muted-foreground">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-3 rounded-sm bg-primary opacity-80"></div>
                    <span class="text-primary">Przychód (Lewa oś)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-1 bg-zinc-500 rounded-full relative flex items-center justify-center">
                        <div class="w-2 h-2 bg-zinc-500 rounded-full"></div>
                    </div>
                    <span>Liczba wizyt (Prawa oś)</span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<style scoped>
:deep(.unovis-axis-label) {
    font-size: 11px;
    font-weight: 800;
    fill: currentColor;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

:deep(.unovis-axis-tick-text) {
    font-size: 10px;
    font-weight: 500;
}
</style>
