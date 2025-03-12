## BambooCMS PHP - A Balanced Approach to DDD in Laravel

![Do not use](https://img.shields.io/badge/Under%20development-Don't%20use-red)
![license](https://img.shields.io/badge/license-MIT-blue.svg)
[![codecov](https://codecov.io/gh/rectitude-open/bamboocms-php/graph/badge.svg?token=OECMR7BR8G)](https://codecov.io/gh/rectitude-open/bamboocms-php)

This project is an implementation of Domain-Driven Design (DDD) based on the Laravel framework. It retains framework features like Eloquent models and form validation while introducing DDD core patterns such as bounded contexts, domain models, and anti-corruption layers. The goal is to balance technical implementation with business representation, providing PHP developers with a gradual reference for applying DDD.

本项目是在 Laravel 框架基础上实现领域驱动设计（DDD）的一种实现。通过保留 Eloquent 模型、表单验证等框架特性，同时引入限界上下文、领域模型、防腐层等 DDD 核心模式，寻求技术实现与业务表达之间的平衡。本项目可为 PHP 开发者提供渐进式 DDD 落地参考。

#### 领域驱动架构

-   通过限界上下文划分业务边界
-   通过值对象和领域服务显式表达业务规则
-   权限控制转变为领域策略 而非横切关注点

#### Laravel 深度整合

-   Eloquent 实现领域持久化
-   原生表单验证（Form Request）与 API 资源输出（Resource）
-   基于 Laravel 队列的领域事件驱动

#### 上下文完整性

-   通过防腐层处理外部服务依赖
-   带上下文日志的业务异常体系
-   与框架解耦的可测试领域层

#### 渐进策略

-   权衡取消部分 DDD 接口
-   突出对中型项目的适配性，尽量避免过度设计
-   可根据项目复杂度，逐步引入更多的 DDD 模式
