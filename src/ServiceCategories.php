<?php

namespace ServiceDirect\Partners;

/**
 * Class ServiceCategories
 * [SOME] of our available service categories
 * See GET https://api.servicedirect.com/resources/service_categories for the most updated list of available service categories
 * @package ServiceDirect\Partners
 */
abstract class ServiceCategories
{
    const AirConditioning = 2;
    const Electrician = 9;
    const Flooring = 12;
    const Handyman = 13;
    const Heating = 14;
    const MoldRemoval = 18;
    const PersonalInjuryAttorney = 109;
    const PestControl = 42;
    const Plumbing = 20;
    const RodentRemoval = 194;
    const WaterDamageRestoration = 27;
    const WildlifeRemoval = 159;
    const Locksmith = 120;
    const Painting = 19;
    const Siding = 25;
}